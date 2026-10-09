<?php

namespace App\Http\Controllers;

use App\Models\PcBuild;
use App\Models\Product;
use App\Models\Sale;
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
        $todaySales = Sale::whereDate('created_at', today())->sum('total_amount');
        $todayOrders = Sale::whereDate('created_at', today())->count();
        $revenue = Sale::sum('total_amount');
        $recentSales = Sale::with('items')->withCount('items')->latest()->limit(10)->get();
        $recentStockIns = StockIn::withCount('items')->latest()->limit(10)->get();
        $storeOrders = $user?->canManageOrders()
            ? StoreOrder::with('items.product')
                ->latest()
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
            'recentSales' => $recentSales,
            'recentStockIns' => $recentStockIns,
            'storeOrders' => $storeOrders,
            'customerStoreOrders' => $customerStoreOrders,
            'customerBuilds' => $customerBuilds,
            'purchaseHistory' => $purchaseHistory,
        ]);
    }

    public function acceptOrder(StoreOrder $storeOrder): RedirectResponse
    {
        $user = auth()->user();

        abort_unless($user && $user->canManageOrders(), 403, 'You are not allowed to accept orders.');
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

        return back()->with('status', 'Order #'.$storeOrder->id.' accepted successfully.');
    }
}
