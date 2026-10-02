<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockIn;
use Illuminate\Http\Request;
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
        $topProducts = SaleItem::query()
            ->select('product_id', 'product_name')
            ->selectRaw('SUM(quantity) AS units_sold, SUM(subtotal) AS sales_total')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get();
        $recentSales = Sale::withCount('items')->latest()->limit(10)->get();
        $recentStockIns = StockIn::withCount('items')->latest()->limit(10)->get();

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
            'user' => $user,
            'products' => $products,
            'lowStock' => $lowStock,
            'todaySales' => $todaySales,
            'todayOrders' => $todayOrders,
            'revenue' => $revenue,
            'topProducts' => $topProducts,
            'recentSales' => $recentSales,
            'recentStockIns' => $recentStockIns,
            'purchaseHistory' => $purchaseHistory,
        ]);
    }
}
