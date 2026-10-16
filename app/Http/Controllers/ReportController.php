<?php

namespace App\Http\Controllers;

use App\Models\CustomerCredit;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
        ]);
        $start = $data['start'] ?? today()->toDateString();
        $end = $data['end'] ?? today()->toDateString();

        $sales = Sale::query()
            ->with('items')
            ->whereBetween('created_at', [$start.' 00:00:00', $end.' 23:59:59'])
            ->latest()
            ->get();
        $expenses = Expense::query()
            ->with('recorder')
            ->whereDate('expense_date', '>=', $start)
            ->whereDate('expense_date', '<=', $end)
            ->orderByDesc('expense_date')
            ->get();
        $credits = CustomerCredit::query()
            ->with('customer')
            ->whereDate('credit_date', '>=', $start)
            ->whereDate('credit_date', '<=', $end)
            ->orderByDesc('credit_date')
            ->get();
        $inventory = Product::query()
            ->with('stockInItems.stockIn')
            ->orderBy('name')
            ->get();
        $inventory->each(function (Product $product): void {
            $product->setAttribute(
                'last_reorder_date',
                $product->stockInItems
                    ->filter(fn ($item) => $item->stockIn)
                    ->sortByDesc(fn ($item) => $item->stockIn->received_at)
                    ->first()?->stockIn?->received_at,
            );
        });
        $lowStock = $inventory
            ->filter(fn (Product $product): bool => $product->stock_quantity <= $product->low_stock_threshold)
            ->values();

        return view('reports', [
            'start' => $start,
            'end' => $end,
            'sales' => $sales,
            'salesTotal' => $sales->sum('total_amount'),
            'expenses' => $expenses,
            'expensesTotal' => $expenses->sum('amount'),
            'credits' => $credits,
            'creditBalanceTotal' => $credits->sum('balance_due'),
            'inventory' => $inventory,
            'lowStock' => $lowStock,
        ]);
    }
}
