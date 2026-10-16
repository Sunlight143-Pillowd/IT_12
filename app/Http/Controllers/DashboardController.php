<?php

namespace App\Http\Controllers;

use App\Models\PcBuild;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockIn;
use App\Models\StoreOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $products = Product::count();
        $lowStock = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();
        $totalOrders = StoreOrder::count();
        $pendingOrders = StoreOrder::where('status', 'pending')->count();
        $acceptedOrders = StoreOrder::where('status', 'accepted')->count();
        $pendingBuildRequests = PcBuild::query()
            ->whereNotNull('user_id')
            ->where('status', 'pending')
            ->count();
        $todaySales = Sale::whereDate('created_at', today())->sum('total_amount');
        $todayOrders = StoreOrder::where('status', 'pending')
            ->whereDate('created_at', today())
            ->count();
        $revenue = Sale::sum('total_amount');
        $salesTrendStart = today()->subDays(6);
        $dailySales = Sale::query()
            ->selectRaw('DATE(created_at) as report_date, SUM(total_amount) as revenue, COUNT(*) as transactions')
            ->whereBetween('created_at', [$salesTrendStart->copy()->startOfDay(), today()->endOfDay()])
            ->groupByRaw('DATE(created_at)')
            ->get()
            ->keyBy('report_date');
        $salesTrend = collect(range(0, 6))
            ->map(function (int $dayOffset) use ($salesTrendStart, $dailySales): array {
                $date = $salesTrendStart->copy()->addDays($dayOffset);
                $daySales = $dailySales->get($date->toDateString());

                return [
                    'date' => $date,
                    'label' => $date->format('D'),
                    'revenue' => (float) ($daySales?->revenue ?? 0),
                    'transactions' => (int) ($daySales?->transactions ?? 0),
                ];
            });
        $salesTrendMaxRevenue = max(1, (float) $salesTrend->max('revenue'));
        $topSellingProducts = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->selectRaw('sale_items.product_id, sale_items.product_name, SUM(sale_items.quantity) as units_sold, SUM(sale_items.subtotal) as sales_total')
            ->whereBetween('sales.created_at', [now()->startOfMonth(), now()])
            ->groupBy('sale_items.product_id', 'sale_items.product_name')
            ->orderByDesc('units_sold')
            ->orderByDesc('sales_total')
            ->limit(5)
            ->get();
        $topSellingMaxUnits = max(1, (int) $topSellingProducts->max('units_sold'));
        $recentSales = Sale::with('items')->withCount('items')->latest()->limit(10)->get();
        $recentStockIns = StockIn::withCount('items')->latest()->limit(10)->get();
        $storeOrders = $user?->canManageOrders()
            ? StoreOrder::with('items.product', 'sale')
                ->latest()
                ->limit(10)
                ->get()
            : collect();
        $recentCustomerBuilds = $user?->canManageOrders()
            ? PcBuild::query()
                ->whereNotNull('user_id')
                ->with('user')
                ->latest()
                ->limit(5)
                ->get()
            : collect();
        $customerStoreOrders = $user && ! $user->canManageOrders()
            ? StoreOrder::with('items')
                ->with('sale')
                ->where(function ($query) use ($user) {
                    $query->whereBelongsTo($user)
                        ->orWhereRaw('LOWER(customer_email) = ?', [strtolower($user->email)]);
                })
                ->latest()
                ->get()
            : collect();
        $customerBuilds = $user && ! $user->canManageOrders()
            ? PcBuild::with('items.product')
                ->where('user_id', $user->id)
                ->latest()
                ->get()
            : collect();
        $purchaseHistory = $user
            ? Sale::where(function ($query) use ($user) {
                $query->where('employee_id', $user->id)
                    ->orWhere('customer_name', $user->name)
                    ->orWhere('customer_name', $user->email);
            })
                ->whereNull('store_order_id')
                ->with('items')
                ->orderByDesc('created_at')
                ->get()
            : collect();

        return view('dashboard', [
            'isAdmin' => $user?->isAdmin() ?? false,
            'isStaff' => $user?->canManageOrders() ?? false,
            'user' => $user,
            'products' => $products,
            'lowStock' => $lowStock,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'acceptedOrders' => $acceptedOrders,
            'pendingBuildRequests' => $pendingBuildRequests,
            'todaySales' => $todaySales,
            'todayOrders' => $todayOrders,
            'revenue' => $revenue,
            'salesTrend' => $salesTrend,
            'salesTrendMaxRevenue' => $salesTrendMaxRevenue,
            'topSellingProducts' => $topSellingProducts,
            'topSellingMaxUnits' => $topSellingMaxUnits,
            'recentSales' => $recentSales,
            'recentStockIns' => $recentStockIns,
            'storeOrders' => $storeOrders,
            'recentCustomerBuilds' => $recentCustomerBuilds,
            'customerStoreOrders' => $customerStoreOrders,
            'customerBuilds' => $customerBuilds,
            'purchaseHistory' => $purchaseHistory,
        ]);
    }

    public function acceptOrder(StoreOrder $storeOrder): RedirectResponse
    {
        $user = auth()->user();

        abort_unless($user && $user->hasPermission('sales.create'), 403, 'You are not allowed to accept orders.');
        DB::transaction(function () use ($storeOrder, $user): void {
            $order = StoreOrder::query()->whereKey($storeOrder->id)->lockForUpdate()->firstOrFail();

            abort_if($order->status !== 'pending', 409, 'This order is no longer pending.');
            abort_if(
                $order->shipping_zone === 'outside_davao' && $order->shipping_fee === null,
                409,
                'Confirm the shipping fee before accepting this order.'
            );

            $order->load('items');
            foreach ($order->items as $item) {
                if (! $item->product_id) {
                    throw ValidationException::withMessages([
                        'order' => ["{$item->product_name} is no longer available. The order was not accepted."],
                    ]);
                }
            }

            $quantities = $order->items->groupBy('product_id')->map(
                fn ($items): int => (int) $items->sum('quantity')
            );
            foreach ($quantities as $productId => $quantity) {
                $product = Product::query()
                    ->whereKey((int) $productId)
                    ->lockForUpdate()
                    ->first();

                if (! $product || ! $product->is_active || $product->availableStock() < $quantity) {
                    throw ValidationException::withMessages([
                        'order' => ["Insufficient stock for {$order->items->firstWhere('product_id', (int) $productId)->product_name}. The order was not accepted."],
                    ]);
                }
            }

            foreach ($quantities as $productId => $quantity) {
                Product::query()->whereKey((int) $productId)->decrement('stock_quantity', $quantity);
            }

            $sale = Sale::create([
                'employee_id' => $user->id,
                'store_order_id' => $order->id,
                'customer_name' => $order->customer_name,
                'total_amount' => $order->total_amount,
                'shipping_fee' => $order->shipping_fee ?? 0,
            ]);

            foreach ($order->items as $item) {
                $sale->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);
            }

            $order->update(['status' => 'accepted']);
        });

        return redirect()->route('dashboard')->with('status', 'Order #'.$storeOrder->id.' accepted successfully.');
    }

    public function updateShippingFee(Request $request, StoreOrder $storeOrder): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user && $user->hasPermission('sales.create'), 403, 'You are not allowed to update order shipping.');

        $validated = $request->validate([
            'shipping_fee' => ['required', 'numeric', 'min:0', 'max:100000'],
        ]);

        DB::transaction(function () use ($storeOrder, $validated): void {
            $order = StoreOrder::query()->whereKey($storeOrder->id)->lockForUpdate()->firstOrFail();

            abort_unless(in_array($order->status, ['pending', 'accepted'], true), 409, 'Shipping can only be updated on pending or accepted orders.');
            abort_unless($order->shipping_zone === 'outside_davao', 409, 'Only outside-Davao orders need a confirmed shipping fee.');

            $subtotal = $order->items()->sum('subtotal');
            $shippingFee = round((float) $validated['shipping_fee'], 2);

            $order->update([
                'shipping_fee' => $shippingFee,
                'total_amount' => $subtotal + $shippingFee,
            ]);

            if ($order->status === 'accepted') {
                $order->sale()->update([
                    'shipping_fee' => $shippingFee,
                    'total_amount' => $subtotal + $shippingFee,
                ]);
            }
        });

        $message = $storeOrder->status === 'accepted'
            ? 'Shipping fee updated for order #'.$storeOrder->id.'.'
            : 'Shipping fee confirmed for order #'.$storeOrder->id.'.';

        return redirect()->route('dashboard')->with('status', $message);
    }

    public function updateAcceptedOrder(Request $request, StoreOrder $storeOrder): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user && $user->hasPermission('sales.create'), 403, 'You are not allowed to update orders.');

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'delivery_address' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($storeOrder, $validated): void {
            $order = StoreOrder::query()->whereKey($storeOrder->id)->lockForUpdate()->firstOrFail();
            abort_unless($order->status === 'accepted', 409, 'Only accepted orders can be edited here.');

            $order->update([
                'customer_name' => trim($validated['customer_name']),
                'customer_email' => trim((string) ($validated['customer_email'] ?? '')) ?: null,
                'customer_phone' => trim((string) ($validated['customer_phone'] ?? '')) ?: null,
                'delivery_address' => trim((string) ($validated['delivery_address'] ?? '')) ?: null,
            ]);
            $order->sale()->update(['customer_name' => $order->customer_name]);
        });

        return redirect()->route('dashboard')->with('status', 'Accepted order #'.$storeOrder->id.' updated successfully.');
    }
}
