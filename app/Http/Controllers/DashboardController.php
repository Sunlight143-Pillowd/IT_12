<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
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
        $topProducts = Product::orderByDesc('stock_quantity')->limit(3)->get();

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
            'purchaseHistory' => $purchaseHistory,
        ]);
    }
}
