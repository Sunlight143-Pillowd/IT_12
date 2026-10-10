<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StoreOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = session()->get('cart', []);
        $products = Product::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $cart = array_intersect_key($cart, $products->all());
        session()->put('cart', $cart);

        $items = $products
            ->only(array_keys($cart))
            ->map(function (Product $product) use ($cart): array {
                $quantity = (int) $cart[$product->id];
                $availableStock = $product->is_active ? $product->availableStock() : 0;

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'available_stock' => $availableStock,
                    'line_total' => $product->price * $quantity,
                ];
            });

        return view('store.cart', [
            'items' => $items,
            'subtotal' => $items->sum('line_total'),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $validated = $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart', []);
        $quantity = (int) ($validated['quantity'] ?? 1);
        $availableStock = $product->availableStock();

        if ($quantity + (int) ($cart[$product->id] ?? 0) > $availableStock) {
            return back()
                ->withErrors(['quantity' => 'The requested quantity exceeds the available stock.'])
                ->withInput();
        }

        $cart[$product->id] = (int) ($cart[$product->id] ?? 0) + $quantity;
        session()->put('cart', $cart);

        return back()->with('status', 'Product added to your cart.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $cart = session()->get('cart', []);
        abort_unless(array_key_exists($product->id, $cart), 404);
        abort_unless($product->is_active, 404);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$product->availableStock()],
        ]);

        $cart[$product->id] = (int) $validated['quantity'];
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Cart quantity updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $cart = session()->get('cart', []);
        unset($cart[$product->id]);
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Product removed from your cart.');
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'payment_method' => ['sometimes', 'required', 'in:cash,gcash,bank_transfer,other'],
            'fulfillment_method' => ['sometimes', 'required', 'in:pickup,delivery'],
            'delivery_address' => ['required_if:fulfillment_method,delivery', 'nullable', 'string', 'max:2000'],
            'shipping_zone' => [
                'required_if:fulfillment_method,delivery',
                'prohibited_unless:fulfillment_method,delivery',
                'nullable',
                'in:davao_city,outside_davao',
            ],
            'shipping_distance_km' => ['required_if:shipping_zone,davao_city', 'nullable', 'numeric', 'gt:0', 'max:1000'],
        ]);

        $cart = session()->get('cart', []);

        if ($cart === []) {
            return redirect()->route('cart.index')->withErrors([
                'order' => 'Add a product to your cart before placing an order.',
            ]);
        }

        $order = DB::transaction(function () use ($request, $validated, $cart): StoreOrder {
            $products = Product::query()
                ->whereIn('id', array_keys($cart))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($cart)) {
                throw ValidationException::withMessages([
                    'order' => 'One or more products in your cart are no longer available. Please review your cart.',
                ]);
            }

            $total = 0;
            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                $quantity = (int) $quantity;

                if (! $product->is_active || $quantity < 1 || $quantity > $product->availableStock()) {
                    throw ValidationException::withMessages([
                        'order' => 'Insufficient stock for '.$product->name.'. Please update your cart and try again.',
                    ]);
                }

                $total += $product->price * $quantity;
            }

            $shippingZone = $validated['shipping_zone'] ?? null;
            $shippingDistance = $shippingZone === 'davao_city'
                ? (float) $validated['shipping_distance_km']
                : null;
            $shippingFee = match ($shippingZone) {
                'davao_city' => 79 + (ceil(max(0, $shippingDistance - 4)) * 15),
                'outside_davao' => null,
                default => 0,
            };

            $order = StoreOrder::create([
                'user_id' => $request->user()?->id,
                'customer_name' => trim($validated['customer_name']),
                'customer_email' => trim($validated['customer_email']),
                'customer_phone' => isset($validated['customer_phone']) ? trim($validated['customer_phone']) : null,
                'total_amount' => $total + ($shippingFee ?? 0),
                'status' => 'pending',
                'payment_method' => $validated['payment_method'] ?? 'cash',
                'fulfillment_method' => $validated['fulfillment_method'] ?? 'pickup',
                'delivery_address' => ($validated['fulfillment_method'] ?? 'pickup') === 'delivery'
                    ? trim($validated['delivery_address'])
                    : null,
                'shipping_zone' => $shippingZone,
                'shipping_distance_km' => $shippingDistance,
                'shipping_fee' => $shippingFee,
            ]);

            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                $quantity = (int) $quantity;
                $subtotal = $product->price * $quantity;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);
            }

            return $order;
        });

        session()->forget('cart');

        $status = 'Order #'.$order->id.' placed successfully. It is pending staff confirmation.';

        if ($order->shipping_zone === 'outside_davao') {
            $status .= ' Staff will confirm the shipping fee before accepting your order.';
        }

        return redirect()->route('cart.index')->with('status', $status);
    }
}
