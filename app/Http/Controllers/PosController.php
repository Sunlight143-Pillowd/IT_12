<?php

namespace App\Http\Controllers;

use App\Models\PcBuild;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $recentSales = Sale::with('items.units', 'items.product')
            ->latest()
            ->limit(10)
            ->get();

        $recentBuilds = PcBuild::with('items.product')
            ->latest()
            ->limit(10)
            ->get();

        return view('pos', compact('recentSales', 'recentBuilds'));
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
