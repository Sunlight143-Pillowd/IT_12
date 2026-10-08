<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\PcBuild;
use App\Models\Product;
use App\Models\Sale;
=======
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
use App\Models\StockIn;
use App\Models\StoreOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
<<<<<<< HEAD
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $products = Product::count();
        $lowStock = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();
        $todaySales = Sale::whereDate('created_at', today())->sum('total_amount');
        $todayOrders = Sale::whereDate('created_at', today())->count();
        $revenue = Sale::sum('total_amount');
<<<<<<< HEAD
        $recentSales = Sale::withCount('items')->latest()->limit(10)->get();
        $recentStockIns = StockIn::withCount('items')->latest()->limit(10)->get();
        $storeOrders = $user?->canManageOrders()
            ? StoreOrder::with('items.product')
                ->latest()
=======
        $topProducts = SaleItem::query()
            ->select('product_id', 'product_name')
            ->selectRaw('SUM(quantity) AS units_sold, SUM(subtotal) AS sales_total')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();
        $recentSales = Sale::withCount('items')->latest()->limit(10)->get();
        $recentStockIns = StockIn::withCount('items')->latest()->limit(10)->get();
        $pendingStoreOrders = $user?->canManageOrders()
            ? StoreOrder::with('items')
                ->where('status', 'pending')
                ->latest()
                ->limit(10)
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                ->get()
            : collect();
        $customerStoreOrders = $user && ! $user->canManageOrders()
            ? StoreOrder::with('items')
                ->where(function ($query) use ($user) {
                    $query->whereBelongsTo($user)
                        ->orWhereRaw('LOWER(customer_email) = ?', [strtolower($user->email)]);
                })
                ->latest()
                ->get()
            : collect();
<<<<<<< HEAD
        $customerBuilds = $user && ! $user->canManageOrders()
            ? PcBuild::with('items.product')
                ->where('user_id', $user->id)
                ->latest()
                ->get()
            : collect();
        $customerBuildManagement = $user?->canManageOrders()
            ? PcBuild::with('items.product')
                ->whereNotNull('user_id')
                ->latest()
                ->get()
            : collect();
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce

        $purchaseHistory = $user
            ? Sale::where(function ($query) use ($user) {
                $query->where('employee_id', $user->id)
                    ->orWhere('customer_name', $user->name)
                    ->orWhere('customer_name', $user->email);
            })
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
            'todaySales' => $todaySales,
            'todayOrders' => $todayOrders,
            'revenue' => $revenue,
<<<<<<< HEAD
            'recentSales' => $recentSales,
            'recentStockIns' => $recentStockIns,
            'storeOrders' => $storeOrders,
            'customerStoreOrders' => $customerStoreOrders,
            'customerBuilds' => $customerBuilds,
            'customerBuildManagement' => $customerBuildManagement,
            'purchaseHistory' => $purchaseHistory,
            'buildStatusOptions' => [
                'pending' => ['accepted', 'cancelled'],
                'accepted' => ['building', 'cancelled'],
                'building' => ['testing', 'cancelled'],
                'testing' => ['ready', 'cancelled'],
                'ready' => ['completed', 'cancelled'],
                'completed' => [],
                'cancelled' => [],
            ],
=======
            'topProducts' => $topProducts,
            'recentSales' => $recentSales,
            'recentStockIns' => $recentStockIns,
            'pendingStoreOrders' => $pendingStoreOrders,
            'customerStoreOrders' => $customerStoreOrders,
            'purchaseHistory' => $purchaseHistory,
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
        ]);
    }

    public function acceptOrder(StoreOrder $storeOrder): RedirectResponse
    {
        $user = auth()->user();

        abort_unless($user && $user->canManageOrders(), 403, 'You are not allowed to accept orders.');
<<<<<<< HEAD
        DB::transaction(function () use ($storeOrder): void {
            $order = StoreOrder::query()->whereKey($storeOrder->id)->lockForUpdate()->firstOrFail();

            abort_if($order->status !== 'pending', 409, 'This order is no longer pending.');

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

            $order->update(['status' => 'accepted']);
        });
=======
        abort_if($storeOrder->status !== 'pending', 409, 'This order is no longer pending.');

        $storeOrder->update(['status' => 'accepted']);
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce

        return back()->with('status', 'Order #'.$storeOrder->id.' accepted successfully.');
    }
}
