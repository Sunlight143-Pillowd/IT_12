<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $quotations = Quotation::with('items')->latest()->limit(10)->get();

        return view('quotations', compact('products', 'quotations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cart_json' => ['required', 'string'],
            'customer_name' => ['nullable', 'string', 'max:150'],
            'customer_contact' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $items = json_decode($data['cart_json'], true);

        if (! is_array($items) || empty($items)) {
            return back()->with('error', 'Add at least one item to the quotation.');
        }

        $total = 0.0;
        foreach ($items as $item) {
            $product = Product::find($item['product_id'] ?? null);
            if (! $product) {
                return back()->with('error', 'One of the quoted products is unavailable.');
            }
            $total += $product->price * ((int) ($item['quantity'] ?? 0));
        }

        return DB::transaction(function () use ($request, $items, $total) {
            $quotation = Quotation::create([
                'employee_id' => auth()->id(),
                'customer_name' => $request->input('customer_name'),
                'customer_contact' => $request->input('customer_contact'),
                'notes' => $request->input('notes'),
                'total_amount' => $total,
            ]);

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (int) ($item['quantity'] ?? 0);
                $subtotal = $product->price * $qty;

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ]);
            }

            return redirect()->route('quotation.index')->with('success', 'Quotation saved successfully.');
        });
    }
}
