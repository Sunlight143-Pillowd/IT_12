<?php

namespace App\Http\Controllers;

use App\Models\PcBuild;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockIn;
use App\Models\StockInItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $recentSales = Sale::with('items.units', 'items.product', 'storeOrder')
            ->latest()
            ->limit(10)
            ->get();

        $recentBuilds = PcBuild::with('items.product')
            ->latest()
            ->limit(10)
            ->get();

        $recentStockIns = StockIn::with('items.product')
            ->latest()
            ->limit(10)
            ->get();
        $recentStockIns->each(static function (StockIn $stockIn): void {
            $stockIn->setAttribute(
                'invoice_total',
                $stockIn->items->sum(fn (StockInItem $item): float => (float) $item->unit_cost * $item->quantity),
            );
        });
        $reportStart = today()->subDays(29);
        $reportEnd = now();
        $dailySales = Sale::query()
            ->select([])
            ->selectRaw('DATE(created_at) as report_date, SUM(total_amount) as amount')
            ->whereBetween('created_at', [$reportStart, $reportEnd])
            ->groupByRaw('DATE(created_at)')
            ->pluck('amount', 'report_date');
        $dailyBuildReceipts = PcBuild::query()
            ->select([])
            ->selectRaw('DATE(stock_deducted_at) as report_date, SUM(total_cost) as amount')
            ->whereNotNull('user_id')
            ->whereNotNull('stock_deducted_at')
            ->whereBetween('stock_deducted_at', [$reportStart, $reportEnd])
            ->groupByRaw('DATE(stock_deducted_at)')
            ->pluck('amount', 'report_date');
        $dailyInvoices = StockIn::query()
            ->join('stock_in_items', 'stock_ins.id', '=', 'stock_in_items.stock_in_id')
            ->select([])
            ->selectRaw('DATE(stock_ins.received_at) as report_date, SUM(stock_in_items.quantity * stock_in_items.unit_cost) as amount')
            ->whereBetween('stock_ins.received_at', [$reportStart, $reportEnd])
            ->groupByRaw('DATE(stock_ins.received_at)')
            ->pluck('amount', 'report_date');
        $posChartData = collect(range(0, 29))
            ->map(function (int $dayOffset) use ($reportStart, $dailySales, $dailyBuildReceipts, $dailyInvoices): array {
                $date = $reportStart->copy()->addDays($dayOffset);
                $dateKey = $date->toDateString();

                return [
                    'label' => $date->format('M j'),
                    'receipts' => (float) $dailySales->get($dateKey, 0) + (float) $dailyBuildReceipts->get($dateKey, 0),
                    'invoices' => (float) $dailyInvoices->get($dateKey, 0),
                ];
            })
            ->values();
        $receiptTotal = (float) $posChartData->sum('receipts');
        $invoiceTotal = (float) $posChartData->sum('invoices');
        $chartTotal = $receiptTotal + $invoiceTotal;
        $receiptSharePercent = $chartTotal > 0 ? round($receiptTotal / $chartTotal * 100, 2) : 0;

        return view('pos', compact(
            'recentSales',
            'recentBuilds',
            'recentStockIns',
            'posChartData',
            'receiptTotal',
            'invoiceTotal',
            'chartTotal',
            'receiptSharePercent',
        ));
    }

    public function receipt(Sale $sale): View
    {
        $sale->load('items.units', 'items.product');

        return view('pos-receipt', compact('sale'));
    }

    public function checkout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cart_json' => ['required', 'string'],
            'customer_name' => ['required', 'string', 'max:150'],
        ]);

        $items = json_decode($data['cart_json'], true);

        if (! is_array($items) || empty($items)) {
            return back()->with('error', 'Your cart is empty.');
        }

        $preparedItems = [];
        $allSerialNumbers = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id'] ?? null);
            if (! $product) {
                return back()->with('error', 'One of the selected products is no longer available.');
            }

            $qty = (int) ($item['quantity'] ?? 0);
            if ($qty <= 0 || $qty > $product->availableStock()) {
                return back()->with('error', 'Insufficient stock for '.$product->name.'.');
            }

            $serialNumbers = $item['serial_numbers'] ?? [];
            if (is_string($serialNumbers)) {
                $serialNumbers = preg_split('/\R/u', $serialNumbers) ?: [];
            }
            if (! is_array($serialNumbers)) {
                return back()->with('error', 'Serial numbers must be entered as a list.');
            }
            $serialNumbers = array_values(array_filter(array_map(
                static fn ($serialNumber): string => trim((string) $serialNumber),
                $serialNumbers,
            ), static fn (string $serialNumber): bool => $serialNumber !== ''));

            if ($product->requires_serial && count($serialNumbers) !== $qty) {
                return back()->with('error', 'Enter one serial number for each '.$product->name.' unit.');
            }

            if (! $product->requires_serial && $serialNumbers !== []) {
                return back()->with('error', $product->name.' does not use serial-number tracking.');
            }

            if (count($serialNumbers) !== count(array_unique($serialNumbers))) {
                return back()->with('error', 'Serial numbers must be unique.');
            }

            if ($serialNumbers !== []) {
                $availableUnits = ProductUnit::query()
                    ->where('product_id', $product->id)
                    ->where('status', 'in_stock')
                    ->whereIn('serial_number', $serialNumbers)
                    ->count();

                if ($availableUnits !== count($serialNumbers)) {
                    return back()->with('error', 'One or more serial numbers do not belong to available '.$product->name.' units.');
                }
            }

            $allSerialNumbers = [...$allSerialNumbers, ...$serialNumbers];
            $preparedItems[] = [
                'product_id' => $product->id,
                'quantity' => $qty,
                'serial_numbers' => $serialNumbers,
            ];
        }

        if (count($allSerialNumbers) !== count(array_unique($allSerialNumbers))) {
            return back()->with('error', 'A serial number cannot be assigned to more than one item.');
        }

        return DB::transaction(function () use ($request, $preparedItems) {
            $total = 0.0;
            $sale = Sale::create([
                'employee_id' => auth()->id(),
                'customer_name' => trim($request->string('customer_name')->toString()),
                'total_amount' => 0,
            ]);

            foreach ($preparedItems as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);
                $qty = $item['quantity'];
                if ($qty > $product->availableStock()) {
                    throw ValidationException::withMessages([
                        'cart_json' => ['Insufficient stock for '.$product->name.'.'],
                    ]);
                }
                $subtotal = $product->price * $qty;
                $total += $subtotal;

                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ]);

                if ($item['serial_numbers'] !== []) {
                    $units = ProductUnit::query()
                        ->where('product_id', $product->id)
                        ->where('status', 'in_stock')
                        ->whereIn('serial_number', $item['serial_numbers'])
                        ->lockForUpdate()
                        ->get();

                    if ($units->count() !== $qty) {
                        throw ValidationException::withMessages([
                            'cart_json' => ['One or more selected serial numbers are no longer available.'],
                        ]);
                    }

                    foreach ($units as $unit) {
                        $unit->update([
                            'sale_item_id' => $saleItem->id,
                            'status' => 'sold',
                            'sold_at' => now(),
                        ]);
                    }
                }

                $product->decrement('stock_quantity', $qty);
            }

            $sale->update(['total_amount' => $total]);

            return redirect()->route('pos.index')->with('success', 'Sale completed successfully.');
        });
    }
}
