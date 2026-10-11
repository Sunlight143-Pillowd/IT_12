<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StoreOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $items = $this->cartItems();

        return view('store.cart', [
            'items' => $items,
            'subtotal' => $items->sum('line_total'),
        ]);
    }

    public function checkout(): View|RedirectResponse
    {
        $items = $this->cartItems();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors([
                'order' => 'Add a product to your cart before checking out.',
            ]);
        }

        return view('store.checkout', [
            'items' => $items,
            'subtotal' => $items->sum('line_total'),
        ]);
    }

    /**
     * @return Collection<int, array{product: Product, quantity: int, available_stock: int, line_total: int}>
     */
    private function cartItems(): Collection
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

        return $items;
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
        $isCheckout = $request->routeIs('checkout.place-order');
        $deliverySelected = $request->input('fulfillment_method') === 'delivery';
        $validated = $request->validate([
            'customer_name' => [$isCheckout ? 'nullable' : 'required_without:first_name', 'string', 'max:255'],
            'first_name' => [$isCheckout ? 'required' : 'nullable', 'string', 'max:128'],
            'last_name' => [$isCheckout ? 'required' : 'nullable', 'string', 'max:128'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => [$isCheckout ? 'required' : 'nullable', 'string', 'max:40'],
            'customer_company' => ['nullable', 'string', 'max:255'],
            'address_line_1' => [$isCheckout ? 'required_if:fulfillment_method,delivery' : 'nullable', 'nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'address_city' => [$isCheckout ? 'required_if:fulfillment_method,delivery' : 'nullable', 'nullable', 'string', 'max:255'],
            'address_province' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'payment_method' => [
                $isCheckout ? 'required' : 'sometimes',
                'in:cash,gcash,bank_transfer,other',
                Rule::when($request->input('shipping_zone') === 'outside_davao', ['not_in:cash']),
            ],
            'fulfillment_method' => [$isCheckout ? 'required' : 'sometimes', 'in:pickup,delivery'],
            'delivery_address' => [
                $isCheckout ? 'nullable' : 'required_if:fulfillment_method,delivery',
                'nullable',
                'string',
                'max:2000',
            ],
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

        $order = DB::transaction(function () use ($request, $validated, $cart, $isCheckout, $deliverySelected): StoreOrder {
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
            $billingAddress = $isCheckout
                ? ($deliverySelected
                    ? collect([
                        $validated['address_line_1'] ?? null,
                        $validated['address_line_2'] ?? null,
                        $validated['address_city'] ?? null,
                        $validated['address_province'] ?? null,
                        $validated['postal_code'] ?? null,
                        'Philippines',
                    ])->filter()->implode(', ')
                    : config('store.address'))
                : null;
            $customerName = isset($validated['first_name'])
                ? trim($validated['first_name'].' '.$validated['last_name'])
                : trim($validated['customer_name']);

            $order = StoreOrder::create([
                'user_id' => $request->user()?->id,
                'customer_name' => $customerName,
                'customer_email' => trim($validated['customer_email']),
                'customer_phone' => isset($validated['customer_phone']) ? trim($validated['customer_phone']) : null,
                'customer_company' => isset($validated['customer_company']) ? trim($validated['customer_company']) : null,
                'billing_address' => $billingAddress,
                'total_amount' => $total + ($shippingFee ?? 0),
                'status' => 'pending',
                'payment_method' => $validated['payment_method']
                    ?? ($shippingZone === 'outside_davao' ? 'gcash' : 'cash'),
                'fulfillment_method' => $validated['fulfillment_method'] ?? 'pickup',
                'delivery_address' => $isCheckout
                    ? $billingAddress
                    : (($validated['fulfillment_method'] ?? 'pickup') === 'delivery'
                        ? trim($validated['delivery_address'])
                        : null),
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
