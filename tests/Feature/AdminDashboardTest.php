<?php

namespace Tests\Feature;

use App\Models\PcBuild;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockReservation;
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

        $category = \App\Models\Category::where('slug', 'gpu')->firstOrFail();

        $this->actingAs($user)
            ->patch('/dashboard/inventory/categories/' . $category->id, ['name' => 'Graphics Card'])
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
        $response->assertSee('/desktops?filter=gaming');
        $response->assertSee('/accessories?filter=displays');
        $response->assertSee('/accessories?filter=peripherals');
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
        $pos->assertSee('Filter by category');
        $pos->assertSee('All Categories');
        $pos->assertSee('product-category-filter');

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

    public function test_custom_pc_build_product_appears_in_desktops_with_spec_summary_in_description(): void
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
