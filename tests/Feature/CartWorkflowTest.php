<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StoreOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_products_and_view_the_cart_total(): void
    {
        $product = Product::factory()->create([
            'name' => 'Guest Cart Product',
            'type' => 'desktop',
            'price' => 1250,
            'stock_quantity' => 4,
        ]);

        $this->get(route('store.top-selling'))
            ->assertOk()
            ->assertSee('Sign In')
            ->assertSee('Register')
            ->assertDontSee('Cart');

        $this->get(route('store.desktops'))
            ->assertSee('Guest Cart Product')
            ->assertSee('Add to cart');

        $this->from(route('store.desktops'))->post(route('cart.items.store', $product), [
            'quantity' => 2,
            'price' => 1,
        ])->assertRedirect(route('store.desktops'));

        $this->assertSame([$product->id => 2], session('cart'));

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Guest Cart Product')
            ->assertSee('₱2,500');

        $this->get(route('home'))
            ->assertDontSee('CART (2)')
            ->assertSee('Sign In')
            ->assertSee('Register');
    }

    public function test_product_cards_add_one_item_and_show_cart_controls_for_existing_items(): void
    {
        $product = Product::factory()->create([
            'name' => 'One Click Product',
            'type' => 'desktop',
            'stock_quantity' => 4,
        ]);

        $this->get(route('store.desktops'))
            ->assertSee('One Click Product')
            ->assertSee('Add to cart')
            ->assertDontSee('Upload Photo')
            ->assertSee('name="quantity" value="1"', false)
            ->assertDontSee('Quantity')
            ->assertDontSee('Decrease quantity of One Click Product')
            ->assertDontSee('Increase quantity of One Click Product')
            ->assertDontSee('Remove One Click Product from cart');

        $this->post(route('cart.items.store', $product))
            ->assertRedirect();

        $this->get(route('store.desktops'))
            ->assertSee('One Click Product')
            ->assertDontSee('Decrease quantity of One Click Product')
            ->assertDontSee('Increase quantity of One Click Product')
            ->assertDontSee('Remove One Click Product from cart')
            ->assertDontSee('name="_method" value="PUT"', false)
            ->assertDontSee('name="_method" value="DELETE"', false);
    }

    public function test_only_admin_can_upload_a_photo_for_catalog_products_without_one(): void
    {
        $employee = User::factory()->create(['email' => 'employee@davaobosscomputer.com']);
        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        Product::factory()->create([
            'name' => 'Desktop Awaiting Photo',
            'type' => 'desktop',
            'image_path' => null,
            'stock_quantity' => 1,
        ]);
        Product::factory()->create([
            'name' => 'Accessory Awaiting Photo',
            'type' => 'accessory',
            'image_path' => null,
            'stock_quantity' => 1,
        ]);

        $this->actingAs($employee)
            ->get(route('store.desktops'))
            ->assertOk()
            ->assertDontSee('Upload Photo')
            ->assertDontSee('Save Photo');

        $this->actingAs($admin)
            ->get(route('store.desktops'))
            ->assertOk()
            ->assertSee('Upload Photo')
            ->assertSee('Save Photo');

        $this->actingAs($employee)
            ->get(route('store.accessories'))
            ->assertOk()
            ->assertDontSee('Upload Photo')
            ->assertDontSee('Save Photo');

        $this->actingAs($admin)
            ->get(route('store.accessories'))
            ->assertOk()
            ->assertSee('Upload Photo')
            ->assertSee('Save Photo');
    }

    public function test_gaming_laptop_cards_do_not_show_quantity_controls(): void
    {
        $product = Product::factory()->create([
            'name' => 'Gaming Laptop Quantity Test',
            'type' => 'laptop',
            'stock_quantity' => 3,
        ]);

        $this->get(route('store.laptops'))
            ->assertOk()
            ->assertDontSee('Decrease quantity of Gaming Laptop Quantity Test')
            ->assertDontSee('Increase quantity of Gaming Laptop Quantity Test')
            ->assertSee('Add to cart');

        $this->post(route('cart.items.store', $product))
            ->assertRedirect();
        $this->assertSame([$product->id => 1], session('cart'));

        $this->from(route('store.laptops'))
            ->put(route('cart.items.update', $product), ['quantity' => 3])
            ->assertRedirect(route('cart.index'));
        $this->assertSame([$product->id => 3], session('cart'));

        $this->get(route('store.laptops'))
            ->assertDontSee('Decrease quantity of Gaming Laptop Quantity Test')
            ->assertDontSee('Increase quantity of Gaming Laptop Quantity Test')
            ->assertDontSee('Remove Gaming Laptop Quantity Test from cart');

        $this->get(route('cart.index'))
            ->assertSee('Decrease quantity of Gaming Laptop Quantity Test')
            ->assertSee('Increase quantity of Gaming Laptop Quantity Test')
            ->assertSee('Remove Gaming Laptop Quantity Test from cart');
    }

    public function test_top_selling_cards_hide_quantity_controls_until_the_product_is_in_the_cart(): void
    {
        $product = Product::factory()->create([
            'name' => 'Top Seller Quantity Test',
            'type' => 'accessory',
            'stock_quantity' => 3,
        ]);
        $user = User::factory()->create();
        $sale = Sale::create([
            'employee_id' => $user->id,
            'customer_name' => 'Top Selling Test Buyer',
            'total_amount' => $product->price,
        ]);
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price,
        ]);

        $this->get(route('store.top-selling'))
            ->assertOk()
            ->assertSee('object-contain')
            ->assertDontSee('Decrease quantity of Top Seller Quantity Test')
            ->assertDontSee('Increase quantity of Top Seller Quantity Test')
            ->assertSee('Add to cart')
            ->assertDontSee('Upload Photo');

        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $this->actingAs($admin)
            ->get(route('store.top-selling'))
            ->assertOk()
            ->assertSee('Upload Photo')
            ->assertSee('Save Photo');
    }

    public function test_signed_in_customer_can_add_products_to_the_cart(): void
    {
        $product = Product::factory()->create(['price' => 1000, 'stock_quantity' => 3]);

        $this->actingAs(User::factory()->create());
        $this->post(route('cart.items.store', $product))->assertRedirect();
        $this->post(route('cart.items.store', $product))->assertRedirect();

        $this->assertSame([$product->id => 2], session('cart'));
        $this->get(route('cart.index'))
            ->assertSee('2')
            ->assertSee('₱2,000')
            ->assertSee('Subtotal')
            ->assertSee('Total');
    }

    public function test_guest_can_update_cart_quantity_and_remove_a_product(): void
    {
        $product = Product::factory()->create(['price' => 1000, 'stock_quantity' => 5]);

        $this->post(route('cart.items.store', $product));

        $this->put(route('cart.items.update', $product), ['quantity' => 3])
            ->assertRedirect(route('cart.index'));
        $this->assertSame([$product->id => 3], session('cart'));
        $this->get(route('cart.index'))
            ->assertSee('₱3,000')
            ->assertSee('₱3,000.00');
        $this->get(route('home'))
            ->assertDontSee('CART (3)')
            ->assertSee('Sign In')
            ->assertSee('Register');

        $this->delete(route('cart.items.destroy', $product))
            ->assertRedirect(route('cart.index'));
        $this->assertSame([], session('cart'));
        $this->get(route('home'))
            ->assertDontSee('CART (0)')
            ->assertSee('Sign In')
            ->assertSee('Register');
    }

    public function test_cart_rejects_quantities_above_available_stock(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 2]);

        $this->from(route('store.desktops'))
            ->post(route('cart.items.store', $product), ['quantity' => 3])
            ->assertRedirect(route('store.desktops'))
            ->assertSessionHasErrors('quantity');

        $this->assertSame([], session('cart', []));
    }

    public function test_cart_does_not_update_to_a_quantity_above_available_stock(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 2]);
        $this->post(route('cart.items.store', $product));

        $this->from(route('cart.index'))
            ->put(route('cart.items.update', $product), ['quantity' => 3])
            ->assertRedirect(route('cart.index'))
            ->assertSessionHasErrors('quantity');

        $this->assertSame([$product->id => 1], session('cart'));
    }

    public function test_cart_displays_product_photo_and_quantity_edit_controls(): void
    {
        $product = Product::factory()->create([
            'name' => 'Cart Photo Product',
            'image_path' => 'products/cart-photo.webp',
            'stock_quantity' => 2,
        ]);

        $this->withSession(['cart' => [$product->id => 2]])
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Cart Photo Product')
            ->assertSee('storage/products/cart-photo.webp')
            ->assertSee('Cart Totals')
            ->assertSee('Proceed to checkout')
            ->assertSee('Subtotal')
            ->assertSee('Total')
            ->assertSee('Decrease quantity of Cart Photo Product')
            ->assertSee('Increase quantity of Cart Photo Product')
            ->assertSee('name="quantity"', false)
            ->assertSee('name="_method" value="PUT"', false)
            ->assertSee('Remove Cart Photo Product from cart');
    }

    public function test_guest_can_place_a_pending_order_from_the_cart(): void
    {
        $product = Product::factory()->create([
            'name' => 'Order Product',
            'price' => 1250,
            'stock_quantity' => 4,
        ]);
        $this->post(route('cart.items.store', $product), ['quantity' => 2]);

        $this->get(route('cart.index'))
            ->assertSee('Proceed to checkout');
        $this->get(route('checkout.index'))
            ->assertSee('Place order')
            ->assertSee('name="first_name"', false)
            ->assertSee('name="customer_email"', false);

        $this->post(route('cart.order'), [
            'customer_name' => 'Guest Buyer',
            'customer_email' => 'guest@example.com',
        ])->assertRedirect(route('cart.index'))
            ->assertSessionHas('status', 'Order #1 placed successfully. It is pending staff confirmation.');

        $this->assertDatabaseHas('store_orders', [
            'id' => 1,
            'user_id' => null,
            'customer_name' => 'Guest Buyer',
            'customer_email' => 'guest@example.com',
            'total_amount' => 2500,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('store_order_items', [
            'store_order_id' => 1,
            'product_id' => $product->id,
            'product_name' => 'Order Product',
            'quantity' => 2,
            'unit_price' => 1250,
            'subtotal' => 2500,
        ]);
        $this->assertSame(4, $product->fresh()->stock_quantity);
        $this->assertSame([], session('cart', []));
    }

    public function test_checkout_displays_the_order_summary_and_billing_form(): void
    {
        $product = Product::factory()->create([
            'name' => 'Checkout Summary Product',
            'price' => 1250,
            'stock_quantity' => 2,
        ]);
        $this->post(route('cart.items.store', $product), ['quantity' => 2]);

        $this->get(route('checkout.index'))
            ->assertSee('Your order')
            ->assertSee('Checkout Summary Product')
            ->assertSee('₱2,500.00')
            ->assertSee('Customer details')
            ->assertSee('Shipping address')
            ->assertSee('Pickup location')
            ->assertSee(config('store.address'))
            ->assertSee('x-show="shippingChoice !== \'pickup\'"', false)
            ->assertSee('x-bind:disabled="shippingChoice === \'outside_davao\'"', false)
            ->assertSee('paymentMethod = \'gcash\'', false)
            ->assertSee('First name')
            ->assertSee('Street address')
            ->assertSee('Place order');
    }

    public function test_checkout_rejects_cash_for_outside_davao_delivery(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 1]);
        $this->post(route('cart.items.store', $product));

        $this->from(route('checkout.index'))->post(route('checkout.place-order'), [
            'first_name' => 'Outside',
            'last_name' => 'Customer',
            'customer_email' => 'outside-checkout@example.com',
            'customer_phone' => '09170000000',
            'address_line_1' => '123 Main Street',
            'address_city' => 'Tagum City',
            'payment_method' => 'cash',
            'fulfillment_method' => 'delivery',
            'shipping_zone' => 'outside_davao',
        ])->assertRedirect(route('checkout.index'))
            ->assertSessionHasErrors('payment_method');

        $this->assertDatabaseCount('store_orders', 0);
        $this->assertSame([$product->id => 1], session('cart'));
    }

    public function test_checkout_place_order_saves_contact_details_and_order_items_for_pickup(): void
    {
        $product = Product::factory()->create(['price' => 500, 'stock_quantity' => 2]);
        $this->post(route('cart.items.store', $product));

        $this->post(route('checkout.place-order'), [
            'first_name' => 'Checkout',
            'last_name' => 'Customer',
            'customer_company' => 'Example Company',
            'customer_email' => 'checkout@example.com',
            'customer_phone' => '09170000000',
            'payment_method' => 'gcash',
            'fulfillment_method' => 'pickup',
            'shipping_zone' => '',
        ])->assertRedirect(route('cart.index'))
            ->assertSessionHas('status', 'Order #1 placed successfully. It is pending staff confirmation.');

        $this->assertDatabaseHas('store_orders', [
            'id' => 1,
            'customer_name' => 'Checkout Customer',
            'customer_email' => 'checkout@example.com',
            'customer_phone' => '09170000000',
            'customer_company' => 'Example Company',
            'billing_address' => config('store.address'),
            'payment_method' => 'gcash',
            'fulfillment_method' => 'pickup',
            'delivery_address' => config('store.address'),
            'total_amount' => 500,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('store_order_items', [
            'store_order_id' => 1,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 500,
            'subtotal' => 500,
        ]);
        $this->assertSame(2, $product->fresh()->stock_quantity);
        $this->assertSame([], session('cart', []));
    }

    public function test_checkout_requires_a_shipping_address_when_delivery_is_selected(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 1]);
        $this->post(route('cart.items.store', $product));

        $this->from(route('checkout.index'))->post(route('checkout.place-order'), [
            'first_name' => 'Checkout',
            'last_name' => 'Customer',
            'customer_email' => 'checkout@example.com',
            'customer_phone' => '09170000000',
            'payment_method' => 'cash',
            'fulfillment_method' => 'delivery',
            'shipping_zone' => 'outside_davao',
        ])->assertRedirect(route('checkout.index'))
            ->assertSessionHasErrors(['address_line_1', 'address_city']);

        $this->assertDatabaseCount('store_orders', 0);
        $this->assertSame([$product->id => 1], session('cart'));
    }

    public function test_checkout_saves_a_shipping_address_when_delivery_is_selected(): void
    {
        $product = Product::factory()->create(['price' => 500, 'stock_quantity' => 2]);
        $this->post(route('cart.items.store', $product));

        $this->post(route('checkout.place-order'), [
            'first_name' => 'Delivery',
            'last_name' => 'Customer',
            'customer_email' => 'delivery-checkout@example.com',
            'customer_phone' => '09170000000',
            'address_line_1' => '123 Main Street',
            'address_line_2' => 'Unit 4',
            'address_city' => 'Davao City',
            'address_province' => 'Davao del Sur',
            'postal_code' => '8000',
            'payment_method' => 'cash',
            'fulfillment_method' => 'delivery',
            'shipping_zone' => 'davao_city',
            'shipping_distance_km' => 3,
        ])->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('store_orders', [
            'customer_name' => 'Delivery Customer',
            'customer_email' => 'delivery-checkout@example.com',
            'billing_address' => '123 Main Street, Unit 4, Davao City, Davao del Sur, 8000, Philippines',
            'delivery_address' => '123 Main Street, Unit 4, Davao City, Davao del Sur, 8000, Philippines',
            'shipping_zone' => 'davao_city',
            'shipping_fee' => 79,
            'total_amount' => 579,
            'status' => 'pending',
        ]);
        $this->assertSame(2, $product->fresh()->stock_quantity);
        $this->assertSame([], session('cart', []));
    }

    public function test_checkout_saves_payment_and_delivery_preferences(): void
    {
        $product = Product::factory()->create(['price' => 500, 'stock_quantity' => 2]);
        $this->post(route('cart.items.store', $product));

        $this->post(route('cart.order'), [
            'customer_name' => 'Delivery Customer',
            'customer_email' => 'delivery@example.com',
            'customer_phone' => '09170000000',
            'payment_method' => 'gcash',
            'fulfillment_method' => 'delivery',
            'shipping_zone' => 'davao_city',
            'shipping_distance_km' => 3,
            'delivery_address' => 'Sandawa, Davao City',
        ])->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('store_orders', [
            'customer_name' => 'Delivery Customer',
            'customer_phone' => '09170000000',
            'payment_method' => 'gcash',
            'fulfillment_method' => 'delivery',
            'shipping_zone' => 'davao_city',
            'shipping_distance_km' => 3,
            'shipping_fee' => 79,
            'total_amount' => 579,
            'delivery_address' => 'Sandawa, Davao City',
            'status' => 'pending',
        ]);
        $this->assertSame(2, $product->fresh()->stock_quantity);
    }

    public function test_davao_city_shipping_uses_the_base_rate_then_charges_for_each_succeeding_kilometer(): void
    {
        $product = Product::factory()->create(['price' => 500, 'stock_quantity' => 2]);
        $this->post(route('cart.items.store', $product));

        $this->post(route('cart.order'), [
            'customer_name' => 'Davao Delivery Customer',
            'customer_email' => 'davao@example.com',
            'fulfillment_method' => 'delivery',
            'shipping_zone' => 'davao_city',
            'shipping_distance_km' => 6,
            'delivery_address' => 'Davao City',
        ])->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('store_orders', [
            'customer_email' => 'davao@example.com',
            'shipping_zone' => 'davao_city',
            'shipping_distance_km' => 6,
            'shipping_fee' => 109,
            'total_amount' => 609,
        ]);
    }

    public function test_davao_city_delivery_requires_a_positive_distance(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 1]);
        $this->post(route('cart.items.store', $product));

        $this->from(route('cart.index'))->post(route('cart.order'), [
            'customer_name' => 'Invalid Distance Customer',
            'customer_email' => 'invalid-distance@example.com',
            'fulfillment_method' => 'delivery',
            'shipping_zone' => 'davao_city',
            'shipping_distance_km' => 0,
            'delivery_address' => 'Davao City',
        ])->assertRedirect(route('cart.index'))
            ->assertSessionHasErrors('shipping_distance_km');

        $this->assertDatabaseCount('store_orders', 0);
        $this->assertSame([$product->id => 1], session('cart'));
    }

    public function test_pickup_orders_cannot_include_a_delivery_shipping_zone(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 1]);
        $this->post(route('cart.items.store', $product));

        $this->from(route('cart.index'))->post(route('cart.order'), [
            'customer_name' => 'Pickup Customer',
            'customer_email' => 'pickup@example.com',
            'fulfillment_method' => 'pickup',
            'shipping_zone' => 'outside_davao',
        ])->assertRedirect(route('cart.index'))
            ->assertSessionHasErrors('shipping_zone');

        $this->assertDatabaseCount('store_orders', 0);
        $this->assertSame([$product->id => 1], session('cart'));
    }

    public function test_outside_davao_order_requires_staff_to_confirm_shipping_before_acceptance(): void
    {
        $product = Product::factory()->create(['price' => 500, 'stock_quantity' => 2]);
        $this->post(route('cart.items.store', $product));
        $this->post(route('cart.order'), [
            'customer_name' => 'Outside Davao Customer',
            'customer_email' => 'outside@example.com',
            'fulfillment_method' => 'delivery',
            'shipping_zone' => 'outside_davao',
            'delivery_address' => 'Tagum City',
        ])->assertRedirect(route('cart.index'))
            ->assertSessionHas('status', 'Order #1 placed successfully. It is pending staff confirmation. Staff will confirm the shipping fee before accepting your order.');

        $this->assertDatabaseHas('store_orders', [
            'customer_email' => 'outside@example.com',
            'payment_method' => 'gcash',
        ]);

        $order = StoreOrder::query()->firstOrFail();
        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $customer = User::factory()->create();

        $this->actingAs($customer)->post(route('dashboard.orders.shipping-fee', $order), [
            'shipping_fee' => 150,
        ])->assertForbidden();
        $this->assertDatabaseHas('store_orders', ['id' => $order->id, 'shipping_fee' => null]);

        $this->actingAs($admin)->post(route('dashboard.orders.accept', $order))
            ->assertConflict();
        $this->assertDatabaseHas('store_orders', [
            'id' => $order->id,
            'status' => 'pending',
            'shipping_fee' => null,
            'total_amount' => 500,
        ]);
        $this->assertSame(2, $product->fresh()->stock_quantity);

        $this->actingAs($admin)->from(route('dashboard'))->post(route('dashboard.orders.shipping-fee', $order), [
            'shipping_fee' => -1,
        ])->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors('shipping_fee');
        $this->assertDatabaseHas('store_orders', ['id' => $order->id, 'shipping_fee' => null]);

        $this->actingAs($admin)->post(route('dashboard.orders.shipping-fee', $order), [
            'shipping_fee' => 150,
        ])->assertRedirect();

        $this->assertDatabaseHas('store_orders', [
            'id' => $order->id,
            'status' => 'pending',
            'shipping_fee' => 150,
            'total_amount' => 650,
        ]);
        $this->actingAs($admin)->post(route('dashboard.orders.accept', $order))
            ->assertRedirect();

        $this->assertDatabaseHas('store_orders', ['id' => $order->id, 'status' => 'accepted', 'total_amount' => 650]);
        $this->assertSame(1, $product->fresh()->stock_quantity);
    }

    public function test_customer_can_place_an_order_linked_to_their_account(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 2]);
        $this->post(route('cart.items.store', $product));

        $this->actingAs($customer)->post(route('cart.order'), [
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
        ])->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('store_orders', [
            'user_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'status' => 'pending',
        ]);

        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $order = StoreOrder::query()->firstOrFail();
        $this->actingAs($admin)->post(route('dashboard.orders.accept', $order))
            ->assertRedirect();

        $this->actingAs($customer)->get(route('dashboard'))
            ->assertSee('My Store Orders')
            ->assertSee('Order #1')
            ->assertSee($product->name.' × 1')
            ->assertSee('Accepted')
            ->assertSee('View receipt #1')
            ->assertSee('1 order(s)');
    }

    public function test_customer_can_see_guest_orders_placed_with_their_account_email(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 2]);
        $this->post(route('cart.items.store', $product));
        $this->post(route('cart.order'), [
            'customer_name' => $customer->name,
            'customer_email' => strtoupper($customer->email),
        ])->assertRedirect(route('cart.index'));

        $this->actingAs($customer)->get(route('dashboard'))
            ->assertSee('My Store Orders')
            ->assertSee('Order #1')
            ->assertSee($product->name.' × 1')
            ->assertSee('Pending');
    }

    public function test_order_is_not_created_when_stock_is_no_longer_available(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 1]);
        $this->withSession(['cart' => [$product->id => 2]]);

        $this->from(route('cart.index'))->post(route('cart.order'), [
            'customer_name' => 'Guest Buyer',
            'customer_email' => 'guest@example.com',
        ])->assertRedirect(route('cart.index'))
            ->assertSessionHasErrors('order');

        $this->assertDatabaseCount('store_orders', 0);
        $this->assertDatabaseCount('store_order_items', 0);
        $this->assertSame([$product->id => 2], session('cart'));
    }

    public function test_order_requires_customer_name_and_valid_email(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 1]);
        $this->post(route('cart.items.store', $product));

        $this->from(route('cart.index'))->post(route('cart.order'), [
            'customer_name' => '',
            'customer_email' => 'not-an-email',
        ])->assertRedirect(route('cart.index'))
            ->assertSessionHasErrors(['customer_name', 'customer_email']);

        $this->assertDatabaseCount('store_orders', 0);
    }

    public function test_order_cannot_be_placed_when_the_cart_is_empty(): void
    {
        $this->from(route('cart.index'))->post(route('cart.order'), [
            'customer_name' => 'Guest Buyer',
            'customer_email' => 'guest@example.com',
        ])->assertRedirect(route('cart.index'))
            ->assertSessionHasErrors('order');

        $this->assertDatabaseCount('store_orders', 0);
    }

    public function test_pending_customer_orders_are_visible_on_the_admin_dashboard(): void
    {
        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $product = Product::factory()->create(['stock_quantity' => 1]);
        $order = StoreOrder::create([
            'customer_name' => 'Dashboard Buyer',
            'customer_email' => 'buyer@example.com',
            'total_amount' => 1000,
            'status' => 'pending',
            'fulfillment_method' => 'pickup',
            'delivery_address' => config('store.address'),
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 1000,
            'subtotal' => 1000,
        ]);

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertSee('Customer Orders')
            ->assertSee('Dashboard Buyer')
            ->assertSee('buyer@example.com')
            ->assertSee('Pickup at: '.config('store.address'))
            ->assertSee($product->name.' × 1');
    }

    public function test_admin_or_employee_can_accept_a_pending_order(): void
    {
        $product = Product::factory()->create(['price' => 1000, 'stock_quantity' => 2]);
        $order = StoreOrder::create([
            'customer_name' => 'Awaiting Approval',
            'customer_email' => 'awaiting@example.com',
            'total_amount' => 1079,
            'status' => 'pending',
            'fulfillment_method' => 'delivery',
            'shipping_zone' => 'davao_city',
            'shipping_distance_km' => 3,
            'shipping_fee' => 79,
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 1000,
            'subtotal' => 1000,
        ]);

        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $employee = User::factory()->create(['email' => 'employee@davaobosscomputer.com']);

        $this->actingAs($admin)->post(route('dashboard.orders.accept', $order))
            ->assertRedirect();
        $this->assertDatabaseHas('store_orders', ['id' => $order->id, 'status' => 'accepted']);
        $this->assertDatabaseHas('sales', [
            'store_order_id' => $order->id,
            'employee_id' => $admin->id,
            'customer_name' => 'Awaiting Approval',
            'total_amount' => 1079,
            'shipping_fee' => 79,
        ]);
        $this->assertDatabaseHas('sale_items', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'subtotal' => 1000,
        ]);
        $this->assertSame(1, $product->fresh()->stock_quantity);

        $this->actingAs($admin)->get(route('pos.index'))
            ->assertSee('Receipts (1)')
            ->assertSee('Receipt #1')
            ->assertSee('Customer order #'.$order->id);
        $this->actingAs($admin)->get(route('pos.receipt', $order->sale))
            ->assertSee('Shipping')
            ->assertSee('₱79.00')
            ->assertSee('₱1,079.00');

        $this->actingAs($admin)->post(route('dashboard.orders.accept', $order))
            ->assertConflict();
        $this->assertSame(1, $product->fresh()->stock_quantity);
        $this->assertDatabaseCount('sales', 1);

        $employeeOrder = StoreOrder::create([
            'customer_name' => 'Employee Acceptance',
            'customer_email' => 'employee-acceptance@example.com',
            'total_amount' => 1000,
            'status' => 'pending',
        ]);
        $employeeOrder->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 1000,
            'subtotal' => 1000,
        ]);

        $this->actingAs($employee)->post(route('dashboard.orders.accept', $employeeOrder))
            ->assertRedirect();
        $this->assertDatabaseHas('store_orders', ['id' => $employeeOrder->id, 'status' => 'accepted']);
        $this->assertDatabaseHas('sales', [
            'store_order_id' => $employeeOrder->id,
            'employee_id' => $employee->id,
        ]);
        $this->assertSame(0, $product->fresh()->stock_quantity);
    }

    public function test_order_is_not_accepted_when_acceptance_cannot_fulfil_the_stock(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 1]);
        $order = StoreOrder::create([
            'customer_name' => 'Short Stock Buyer',
            'customer_email' => 'short-stock@example.com',
            'total_amount' => 2000,
            'status' => 'pending',
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 1000,
            'subtotal' => 2000,
        ]);
        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);

        $this->actingAs($admin)
            ->from(route('dashboard'))
            ->post(route('dashboard.orders.accept', $order))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors('order');

        $this->assertDatabaseHas('store_orders', ['id' => $order->id, 'status' => 'pending']);
        $this->assertSame(1, $product->fresh()->stock_quantity);
    }
}
