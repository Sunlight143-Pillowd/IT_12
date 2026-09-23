<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->get();

        return view('pos', compact('products'));
    }

    public function checkout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cart_json' => ['required', 'string'],
            'customer_name' => ['nullable', 'string', 'max:150'],
        ]);

        $items = json_decode($data['cart_json'], true);

        if (! is_array($items) || empty($items)) {
            return back()->with('error', 'Your cart is empty.');
        }

        $total = 0.0;

        foreach ($items as $item) {
            $product = Product::find($item['product_id'] ?? null);
            if (! $product) {
                return back()->with('error', 'One of the selected products is no longer available.');
            }

            $qty = (int) ($item['quantity'] ?? 0);
            if ($qty <= 0 || $qty > $product->stock_quantity) {
                return back()->with('error', 'Insufficient stock for ' . $product->name . '.');
            }

            $subtotal = $product->price * $qty;
            $total += $subtotal;
        }

        return DB::transaction(function () use ($request, $items, $total) {
            $sale = Sale::create([
                'employee_id' => auth()->id(),
                'customer_name' => $request->input('customer_name'),
                'total_amount' => $total,
            ]);

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (int) ($item['quantity'] ?? 0);
                $subtotal = $product->price * $qty;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock_quantity', $qty);
            }

            return redirect()->route('pos.index')->with('success', 'Sale completed successfully.');
        });
    }
}
