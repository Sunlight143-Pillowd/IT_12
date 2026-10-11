<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PcBuild;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockReservation;
use App\Models\StoreOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_sees_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Site Admin',
            'email' => 'admin@davaobosscomputer.com',
            'password' => bcrypt('admin123'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Site Admin');
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Log Out');
        $response->assertDontSee('Control your store from one dashboard.');
        $response->assertSee('Customer Orders');
        $response->assertDontSee('Customer Build / PC Build Management');
        $response->assertSee('Live');
        $response->assertSee('Total Products');
        $response->assertSee('Total Orders');
        $response->assertSee('Pending Orders');
        $response->assertSee('Accepted Orders');
        $response->assertSee('Low Stock');
        $response->assertSee('Sales Today');
        $response->assertSee('Total Revenue');
        $response->assertSee('Revenue');
        $response->assertSee('Recent customer build requests');
        $response->assertSee('VIEW INVENTORY');
        $response->assertSee('OPEN POS');
    }

    public function test_dashboard_summary_counts_match_saved_products_orders_and_requests(): void
    {
        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $customer = User::factory()->create(['email' => 'requester@example.com']);
        Product::factory()->create(['stock_quantity' => 1, 'low_stock_threshold' => 2]);
        Product::factory()->create(['stock_quantity' => 8, 'low_stock_threshold' => 2]);
        StoreOrder::create([
            'customer_name' => 'Pending dashboard order',
            'customer_email' => 'pending@example.com',
            'total_amount' => 100,
            'status' => 'pending',
        ]);
        StoreOrder::create([
            'customer_name' => 'Accepted dashboard order',
            'customer_email' => 'accepted@example.com',
            'total_amount' => 200,
            'status' => 'accepted',
        ]);
        PcBuild::create([
            'build_number' => 'PC-DASHBOARD-REQUEST',
            'customer_name' => 'Dashboard Requester',
            'customer_email' => $customer->email,
            'user_id' => $customer->id,
            'status' => 'pending',
            'notes' => 'CPU: Custom processor',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertViewHas('products', 2)
            ->assertViewHas('totalOrders', 2)
            ->assertViewHas('pendingOrders', 1)
            ->assertViewHas('acceptedOrders', 1)
            ->assertViewHas('lowStock', 1)
            ->assertViewHas('pendingBuildRequests', 1)
            ->assertSee('Pending dashboard order')
            ->assertSee('PC-DASHBOARD-REQUEST')
            ->assertSee('CPU: Custom processor');
    }

    public function test_dashboard_today_metrics_only_include_orders_and_sales_from_today(): void
    {
        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);

        $this->travelTo('2026-10-11 12:00:00');
        Sale::create([
            'employee_id' => $admin->id,
            'customer_name' => 'Yesterday Sale',
            'total_amount' => 1000,
        ]);
        StoreOrder::create([
            'customer_name' => 'Yesterday Order',
            'customer_email' => 'yesterday@example.com',
            'total_amount' => 1000,
            'status' => 'pending',
        ]);

        $this->travelTo('2026-10-12 12:00:00');
        Sale::create([
            'employee_id' => $admin->id,
            'customer_name' => 'Today Sale',
            'total_amount' => 500,
        ]);
        $acceptedProduct = Product::factory()->create(['stock_quantity' => 2]);
        $acceptedOrder = StoreOrder::create([
            'customer_name' => 'Today Accepted Order',
            'customer_email' => 'today-accepted@example.com',
            'total_amount' => 900,
            'status' => 'pending',
        ]);
        $acceptedOrder->items()->create([
            'product_id' => $acceptedProduct->id,
            'product_name' => $acceptedProduct->name,
            'quantity' => 1,
            'unit_price' => 900,
            'subtotal' => 900,
        ]);
        StoreOrder::create([
            'customer_name' => 'Today Pending Order',
            'customer_email' => 'today-pending@example.com',
            'total_amount' => 750,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('dashboard.orders.accept', $acceptedOrder))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('store_orders', [
            'id' => $acceptedOrder->id,
            'status' => 'accepted',
        ]);
        $this->assertDatabaseHas('sales', [
            'store_order_id' => $acceptedOrder->id,
            'total_amount' => 900,
        ]);
        $this->actingAs($admin)
            ->get(route('pos.index'))
            ->assertSee('Customer order #'.$acceptedOrder->id);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertSeeInOrder([
                'Sales Today',
                '₱1,400',
                'Pending Orders Today',
                '1',
                'Low Stock',
                'Total Revenue',
                '₱2,400',
            ])
            ->assertViewHas('todayOrders', 1)
            ->assertViewHas('todaySales', 1400);
    }

    public function test_admin_navigation_links_are_visible_in_the_navbar(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@davaobosscomputer.com',
        ]);

        $content = $this->actingAs($admin)->get(route('dashboard'))->assertOk()->getContent();
        $this->assertStringContainsString('aria-label="Primary navigation"', $content);
        $this->assertStringNotContainsString('aria-label="Toggle navigation"', $content);
        $this->assertStringNotContainsString('app-responsive-navigation', $content);

        $navigationStart = strpos($content, 'class="app-desktop-navigation');
        $navigationEnd = strpos($content, '</nav>', $navigationStart);

        $this->assertNotFalse($navigationStart);
        $this->assertNotFalse($navigationEnd);

        $navigation = substr($content, $navigationStart, $navigationEnd - $navigationStart);
        $expectedLinks = [
            'Store',
            'Desktops',
            'Laptops',
            'Categories',
            'Top Selling',
            'Build PC',
            'Special offers',
            'Dashboard',
        ];
        $previousPosition = -1;

        foreach ($expectedLinks as $label) {
            $position = strpos($navigation, $label);

            $this->assertNotFalse($position, "{$label} should be in the admin navbar.");
            $this->assertGreaterThan($previousPosition, $position, "{$label} should appear in the requested navigation order.");
            $previousPosition = $position;
        }

        foreach (['Inventory', 'Stock In', 'POS', 'Quotations', 'Build PC Orders'] as $label) {
            $this->assertStringNotContainsString($label, $navigation);
        }
    }

    public function test_customer_navigation_includes_my_orders_and_cart_in_the_navbar(): void
    {
        $customer = User::factory()->create([
            'email' => 'customer@example.com',
        ]);

        $content = $this->actingAs($customer)->get(route('dashboard'))->assertOk()->getContent();
        $navigationStart = strpos($content, 'class="app-desktop-navigation');
        $navigationEnd = strpos($content, '</nav>', $navigationStart);

        $this->assertNotFalse($navigationStart);
        $this->assertNotFalse($navigationEnd);

        $navigation = substr($content, $navigationStart, $navigationEnd - $navigationStart);

        $this->assertStringContainsString('My Orders', $navigation);
        $this->assertStringContainsString('href="'.route('dashboard').'"', $navigation);
        $this->assertStringContainsString('Cart', $navigation);
        $this->assertStringNotContainsString('Stock In', $navigation);
        $this->assertStringNotContainsString('Quotations', $navigation);
    }

    public function test_regular_user_sees_purchase_history_and_back_button(): void
    {
        $user = User::factory()->create([
            'name' => 'Francisco',
            'email' => 'francisco@example.com',
            'password' => bcrypt('password'),
        ]);

        $sale = Sale::create([
            'employee_id' => $user->id,
            'customer_name' => $user->name,
            'total_amount' => 2499.00,
        ]);

        $sale->items()->create([
            'product_id' => Product::create([
                'name' => 'Wireless Mouse',
                'slug' => 'wireless-mouse',
                'type' => 'accessory',
                'category' => 'Peripherals',
                'price' => 2499,
                'stock_quantity' => 10,
                'low_stock_threshold' => 2,
                'stock_location' => 'store',
                'description' => 'Gaming mouse',
                'is_active' => true,
            ])->id,
            'product_name' => 'Wireless Mouse',
            'unit_price' => 2499.00,
            'quantity' => 1,
            'subtotal' => 2499.00,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Purchase History');
        $response->assertSee('Wireless Mouse');
        $response->assertSee('Back to Main Dashboard');
    }

    public function test_pos_shows_receipts_and_build_orders_without_checkout_controls(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Receipt Test Keyboard',
            'slug' => 'receipt-test-keyboard',
            'type' => 'accessory',
            'category' => 'Peripherals',
            'price' => 1499,
            'stock_quantity' => 5,
            'low_stock_threshold' => 1,
            'stock_location' => 'store',
            'description' => 'Mechanical keyboard',
            'is_active' => true,
        ]);
        $sale = Sale::create([
            'employee_id' => $user->id,
            'customer_name' => 'Receipt Customer',
            'total_amount' => 2998.00,
        ]);
        $sale->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Receipt Test Keyboard',
            'unit_price' => 1499.00,
            'quantity' => 2,
            'subtotal' => 2998.00,
        ]);
        $build = PcBuild::create([
            'build_number' => 'PC-RECEIPT-0001',
            'customer_name' => 'Build Customer',
            'employee_id' => $user->id,
            'status' => 'reserved',
            'total_cost' => 1499.00,
            'notes' => 'Gaming setup',
        ]);
        $build->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 1499.00,
            'subtotal' => 1499.00,
        ]);

        $response = $this->actingAs($user)->get('/dashboard/pos');

        $response->assertOk();
        $response->assertSee('href="'.route('dashboard').'"', false);
        $response->assertSee('Back to Dashboard');
        $response->assertSee('Receipts');
        $response->assertSee('Computer Build Orders');
        $response->assertSee('Receipt #'.$sale->id);
        $response->assertSee('Receipt Customer');
        $response->assertSee('Receipt Test Keyboard');
        $response->assertSee('data-detail-template="receipt-detail-'.$sale->id.'"', false);
        $response->assertSee('id="receipt-detail-'.$sale->id.'"', false);
        $response->assertSee('1,499.00 × 2');
        $response->assertSee('₱2,998.00');
        $response->assertSee($build->build_number);
        $response->assertSee('Build Customer');
        $response->assertSee('Gaming setup');
        $response->assertSee('data-detail-template="build-detail-'.$build->id.'"', false);
        $response->assertSee('id="build-detail-'.$build->id.'"', false);
        $response->assertSee('₱1,499.00 × 1');
        $response->assertSee('id="selected-order-details"', false);
        $response->assertSee('lg:grid-cols-3');
        $response->assertSee('lg:col-span-2');
        $response->assertDontSee('<dialog', false);
        $response->assertDontSee('Products');
        $response->assertDontSee('Complete Sale');
    }

    public function test_admin_can_add_and_edit_categories(): void
    {
        $user = User::factory()->create([
            'name' => 'Site Admin',
            'email' => 'admin@davaobosscomputer.com',
            'password' => bcrypt('admin123'),
        ]);

        $this->actingAs($user)
            ->post('/dashboard/inventory/categories', ['name' => 'GPU'])
            ->assertRedirect('/dashboard/inventory');

        $category = Category::where('slug', 'gpu')->firstOrFail();

        $this->actingAs($user)
            ->patch('/dashboard/inventory/categories/'.$category->id, ['name' => 'Graphics Card'])
            ->assertRedirect('/dashboard/inventory');

        $category->refresh();

        $this->assertSame('Graphics Card', $category->name);
        $this->assertSame('graphics-card', $category->slug);
    }

    public function test_category_links_route_to_matching_product_pages(): void
    {
        Product::create([
            'name' => 'RTX 4080',
            'slug' => 'rtx-4080',
            'type' => 'desktop',
            'category' => 'gaming',
            'price' => 49999,
            'stock_quantity' => 8,
            'low_stock_threshold' => 2,
            'stock_location' => 'warehouse',
            'description' => 'Graphics card',
            'is_active' => true,
        ]);

        Product::create([
            'name' => '27" Monitor',
            'slug' => '27-monitor',
            'type' => 'accessory',
            'category' => 'displays',
            'price' => 12999,
            'stock_quantity' => 5,
            'low_stock_threshold' => 2,
            'stock_location' => 'store',
            'description' => 'Display',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Mechanical Keyboard',
            'slug' => 'mechanical-keyboard',
            'type' => 'accessory',
            'category' => 'peripherals',
            'price' => 3499,
            'stock_quantity' => 12,
            'low_stock_threshold' => 2,
            'stock_location' => 'store',
            'description' => 'Keyboard',
            'is_active' => true,
        ]);

        $response = $this->get('/categories');

        $response->assertOk();
        $response->assertSee('/categories/gaming');
        $response->assertSee('/categories/displays');
        $response->assertSee('/categories/peripherals');

        $this->get(route('store.category', ['categorySlug' => 'gaming']))
            ->assertOk()
            ->assertSee('RTX 4080')
            ->assertDontSee('27&quot; Monitor')
            ->assertDontSee('Mechanical Keyboard');
    }

    public function test_category_cards_keep_their_actual_labels_for_desktop_and_laptop_types(): void
    {
        Product::create([
            'name' => 'Performance Tower',
            'slug' => 'performance-tower',
            'type' => 'desktop',
            'category' => 'Performance',
            'price' => 59999,
            'stock_quantity' => 3,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'description' => 'Performance gaming desktop',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Ready to Ship PC',
            'slug' => 'ready-to-ship-pc',
            'type' => 'desktop',
            'category' => 'Ready to Ship',
            'price' => 45999,
            'stock_quantity' => 6,
            'low_stock_threshold' => 2,
            'stock_location' => 'warehouse',
            'description' => 'Ready to ship desktop',
            'is_active' => true,
        ]);

        $response = $this->get('/categories');

        $response->assertOk();
        $response->assertSee('Performance');
        $response->assertSee('Ready to Ship');
    }

    public function test_inventory_type_dropdown_lists_pc_related_types(): void
    {
        $user = User::factory()->create([
            'name' => 'Site Admin',
            'email' => 'admin@davaobosscomputer.com',
            'password' => bcrypt('admin123'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard/inventory');

        $response->assertOk();
        $response->assertSee('Desktop');
        $response->assertSee('Laptop');
        $response->assertSee('Accessory');
        $response->assertSee('GPU');
        $response->assertSee('CPU');
        $response->assertSee('Monitor');
        $response->assertSee('Mouse');
        $response->assertSee('Keyboard');
        $response->assertSee('Case');
        $response->assertSee('Fan');
        $response->assertSee('CPU Cooler');
        $response->assertSee('SSD');
        $response->assertSee('RAM');
    }

    public function test_inventory_page_removes_pill_filters_and_pos_quotation_use_dropdown_filters(): void
    {
        $user = User::factory()->create([
            'name' => 'Site Admin',
            'email' => 'admin@davaobosscomputer.com',
            'password' => bcrypt('admin123'),
        ]);

        Product::create([
            'name' => 'RTX 4080',
            'slug' => 'rtx-4080',
            'type' => 'desktop',
            'category' => 'GPU',
            'price' => 49999,
            'stock_quantity' => 8,
            'low_stock_threshold' => 2,
            'stock_location' => 'warehouse',
            'description' => 'Graphics card',
            'is_active' => true,
        ]);

        $inventory = $this->actingAs($user)->get('/dashboard/inventory');
        $inventory->assertOk();
        $inventory->assertDontSee('rounded-full border px-3 py-1.5 text-xs font-semibold');

        $pos = $this->actingAs($user)->get('/dashboard/pos');
        $pos->assertOk();
        $pos->assertSee('Receipts');
        $pos->assertSee('Computer Build Orders');
        $pos->assertDontSee('Products');
        $pos->assertDontSee('Cart');
        $pos->assertDontSee('Complete Sale');

        $quotation = $this->actingAs($user)->get('/dashboard/quotations');
        $quotation->assertOk();
        $quotation->assertSee('Filter by category');
        $quotation->assertSee('All Categories');
        $quotation->assertSee('quote-category-filter');
    }

    public function test_admin_can_save_a_pc_build_and_reserve_stock_without_deducting_actual_inventory(): void
    {
        $user = User::factory()->create([
            'name' => 'Site Admin',
            'email' => 'admin@davaobosscomputer.com',
            'password' => bcrypt('admin123'),
        ]);

        $cpu = Product::create([
            'name' => 'Ryzen 5 5600',
            'slug' => 'ryzen-5-5600',
            'type' => 'cpu',
            'category' => 'CPU',
            'price' => 12000,
            'stock_quantity' => 5,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'description' => 'Desktop CPU',
            'is_active' => true,
        ]);

        $gpu = Product::create([
            'name' => 'RTX 4060',
            'slug' => 'rtx-4060',
            'type' => 'gpu',
            'category' => 'GPU',
            'price' => 28000,
            'stock_quantity' => 7,
            'low_stock_threshold' => 2,
            'stock_location' => 'warehouse',
            'description' => 'Graphics card',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/dashboard/build-pc', [
            'customer_name' => 'John Doe',
            'notes' => 'Gaming configuration',
            'items' => [
                ['product_id' => $cpu->id, 'quantity' => 1],
                ['product_id' => $gpu->id, 'quantity' => 1],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pc_builds', ['customer_name' => 'John Doe']);
        $this->assertDatabaseHas('stock_reservations', ['status' => 'active']);
        $this->assertSame(7, Product::find($gpu->id)->stock_quantity);
        $this->assertSame(1, StockReservation::where('product_id', $gpu->id)->count());
        $this->assertSame(1, PcBuild::count());
    }

    public function test_admin_custom_pc_build_creates_a_storefront_product_for_desktops(): void
    {
        $user = User::factory()->create([
            'name' => 'Site Admin',
            'email' => 'admin@davaobosscomputer.com',
            'password' => bcrypt('admin123'),
        ]);

        $cpu = Product::create([
            'name' => 'Ryzen 5 5600',
            'slug' => 'ryzen-5-5600',
            'type' => 'cpu',
            'category' => 'CPU',
            'price' => 12000,
            'stock_quantity' => 5,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'description' => 'Desktop CPU',
            'is_active' => true,
        ]);

        $gpu = Product::create([
            'name' => 'RTX 4060',
            'slug' => 'rtx-4060',
            'type' => 'gpu',
            'category' => 'GPU',
            'price' => 28000,
            'stock_quantity' => 7,
            'low_stock_threshold' => 2,
            'stock_location' => 'warehouse',
            'description' => 'Graphics card',
            'is_active' => true,
        ]);

        $this->actingAs($user)->post('/dashboard/build-pc', [
            'customer_name' => 'John Doe',
            'notes' => 'Gaming configuration',
            'items' => [
                ['product_id' => $cpu->id, 'quantity' => 1],
                ['product_id' => $gpu->id, 'quantity' => 1],
            ],
        ]);

        $response = $this->get('/desktops');

        $response->assertOk();
        $response->assertSee('Custom PC Build');
        $response->assertSee('Ryzen 5 5600');
        $response->assertSee('RTX 4060');
    }
}
