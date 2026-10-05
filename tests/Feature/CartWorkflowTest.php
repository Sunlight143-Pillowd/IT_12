<?php

namespace Tests\Feature;

use App\Models\Product;
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
            ->assertSee('CART (2)')
            ->assertSee('Add to cart');
    }

    public function test_signed_in_customer_can_add_products_to_the_cart(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 3]);

        $this->actingAs(User::factory()->create())
            ->post(route('cart.items.store', $product))
            ->assertRedirect();

        $this->assertSame([$product->id => 1], session('cart'));
    }

    public function test_guest_can_update_cart_quantity_and_remove_a_product(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 5]);

        $this->post(route('cart.items.store', $product));

        $this->put(route('cart.items.update', $product), ['quantity' => 3])
            ->assertRedirect(route('cart.index'));
        $this->assertSame([$product->id => 3], session('cart'));

        $this->delete(route('cart.items.destroy', $product))
            ->assertRedirect(route('cart.index'));
        $this->assertSame([], session('cart'));
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

    public function test_guest_can_place_a_pending_order_from_the_cart(): void
    {
        $product = Product::factory()->create([
            'name' => 'Order Product',
            'price' => 1250,
            'stock_quantity' => 4,
        ]);
        $this->post(route('cart.items.store', $product), ['quantity' => 2]);

        $this->get(route('cart.index'))
            ->assertSee('Place order')
            ->assertSee('customer_name')
            ->assertSee('customer_email');

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

        $this->actingAs($customer)->get(route('dashboard'))
            ->assertSee('My Store Orders')
            ->assertSee('Order #1')
            ->assertSee($product->name.' × 1')
            ->assertSee('Pending');
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
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 1000,
            'subtotal' => 1000,
        ]);

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertSee('Pending Customer Orders')
            ->assertSee('Dashboard Buyer')
            ->assertSee('buyer@example.com')
            ->assertSee($product->name.' × 1');
    }

    public function test_admin_or_employee_can_accept_a_pending_order(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 1]);
        $order = StoreOrder::create([
            'customer_name' => 'Awaiting Approval',
            'customer_email' => 'awaiting@example.com',
            'total_amount' => 1000,
            'status' => 'pending',
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

        $order->refresh();
        $this->assertSame('accepted', $order->status);

        $order->update(['status' => 'pending']);

        $this->actingAs($employee)->post(route('dashboard.orders.accept', $order))
            ->assertRedirect();
        $this->assertDatabaseHas('store_orders', ['id' => $order->id, 'status' => 'accepted']);
    }
}
