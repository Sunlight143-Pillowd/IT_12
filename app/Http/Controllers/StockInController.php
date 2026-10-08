<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\StockIn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockInController extends Controller
{
    public function index(): View
    {
        $products = Product::query()->where('is_active', true)->orderBy('name')->get();
        $stockIns = StockIn::with('items.product', 'employee')->latest()->limit(20)->get();

        return view('stock-in', compact('products', 'stockIns'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_name' => ['required', 'string', 'max:150'],
            'supplier_contact' => ['nullable', 'string', 'max:150'],
            'invoice_number' => ['required', 'string', 'max:100', Rule::unique('stock_ins')->where('supplier_name', $request->input('supplier_name'))],
            'received_at' => ['required', 'date'],
            'delivery_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'before_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'after_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10000'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.warranty_months' => ['nullable', 'integer', 'min:0', 'max:1200'],
            'items.*.serial_numbers' => ['nullable', 'string', 'max:100000'],
        ]);

        $preparedItems = [];
        $allSerialNumbers = [];

        foreach ($data['items'] as $index => $item) {
            $serialNumbers = array_values(array_filter(array_map(
                static fn (string $serialNumber): string => trim($serialNumber),
                preg_split('/\R/u', $item['serial_numbers'] ?? '') ?: [],
            ), static fn (string $serialNumber): bool => $serialNumber !== ''));
            $product = Product::findOrFail($item['product_id']);

            if ($product->requires_serial && count($serialNumbers) !== (int) $item['quantity']) {
                throw ValidationException::withMessages([
                    "items.$index.serial_numbers" => ["Enter one serial number for each {$product->name} unit received."],
                ]);
            }

            if ($serialNumbers !== [] && count($serialNumbers) !== (int) $item['quantity']) {
                throw ValidationException::withMessages([
                    "items.$index.serial_numbers" => ['Serial number count must match the received quantity.'],
                ]);
            }

            $preparedItems[] = [
                ...$item,
                'serial_numbers' => $serialNumbers,
                'product' => $product,
            ];
            $allSerialNumbers = [...$allSerialNumbers, ...$serialNumbers];
        }

        $duplicateSerials = count($allSerialNumbers) !== count(array_unique($allSerialNumbers));
        $existingSerial = $allSerialNumbers !== [] && ProductUnit::whereIn('serial_number', $allSerialNumbers)->exists();

        if ($duplicateSerials || $existingSerial) {
            throw ValidationException::withMessages([
                'items' => ['Serial numbers must be unique across all received units.'],
            ]);
        }

        DB::transaction(function () use ($request, $data, $preparedItems): void {
            $stockIn = StockIn::create([
                'employee_id' => $request->user()->id,
                'supplier_name' => $data['supplier_name'],
                'supplier_contact' => $data['supplier_contact'] ?? null,
                'invoice_number' => $data['invoice_number'],
                'received_at' => $data['received_at'],
                'notes' => $data['notes'] ?? null,
                'delivery_document_path' => $request->file('delivery_document')?->store('stock-ins/documents', 'public'),
                'before_photo_path' => $request->file('before_photo')?->store('stock-ins/photos', 'public'),
                'after_photo_path' => $request->file('after_photo')?->store('stock-ins/photos', 'public'),
            ]);

            foreach ($preparedItems as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $warrantyMonths = $item['warranty_months'] ?? $product->warranty_months;

                $stockInItem = $stockIn->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $item['unit_cost'],
                    'warranty_months' => $warrantyMonths,
                ]);

                foreach (range(0, $quantity - 1) as $unitIndex) {
                    ProductUnit::create([
                        'stock_in_item_id' => $stockInItem->id,
                        'product_id' => $product->id,
                        'serial_number' => $item['serial_numbers'][$unitIndex] ?? null,
                        'warranty_months' => $warrantyMonths,
                        'status' => 'in_stock',
                    ]);
                }

                $product->increment('stock_quantity', $quantity);
            }
        });

        return redirect()->route('stock-in.index')->with('success', 'Delivery recorded and inventory stock updated.');
    }
}
