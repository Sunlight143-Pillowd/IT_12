<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_products_are_rendered_on_the_homepage(): void
    {
        $this->seed();

        $this->get('/')->assertSee('Boss Apex 4K');
        $this->assertDatabaseHas('categories', ['slug' => 'ready-to-ship']);
    }

    public function test_seeding_preserves_sales_and_does_not_duplicate_catalog_products(): void
    {
        $product = Product::create([
            'name' => 'Existing Store Product',
            'slug' => 'existing-store-product',
            'type' => 'accessory',
            'category' => 'Peripherals',
            'price' => 1000,
            'stock_quantity' => 5,
            'low_stock_threshold' => 1,
            'stock_location' => 'store',
            'is_active' => true,
        ]);
        $user = User::factory()->create();
        $sale = Sale::create([
            'employee_id' => $user->id,
            'customer_name' => 'Store Customer',
            'total_amount' => 1000,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price,
        ]);
        $legacyCatalogProduct = Product::create([
            'name' => 'Boss Strike ITX',
            'slug' => 'boss-strike-itx-123',
            'type' => 'desktop',
            'category' => 'Ready to Ship',
            'price' => 54999,
            'stock_quantity' => 6,
            'low_stock_threshold' => 5,
            'stock_location' => 'warehouse',
            'is_active' => true,
        ]);

        $this->seed();
        Product::where('slug', 'boss-apex-4k')->update(['name' => 'Renamed Boss Apex 4K']);
        $this->seed();
        $this->seed();

        $this->assertModelExists($product);
        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
        ]);
        $this->assertSame(1, Product::where('slug', 'boss-apex-4k')->count());
        $this->assertSame('Renamed Boss Apex 4K', Product::where('slug', 'boss-apex-4k')->value('name'));
        $this->assertModelExists($legacyCatalogProduct);
        $this->assertSame(1, Product::where('name', 'Boss Strike ITX')->count());
        $this->assertSame(6, $legacyCatalogProduct->fresh()->stock_quantity);
        $this->assertSame(1, Category::where('slug', 'ready-to-ship')->count());
    }

    public function test_inactive_products_are_hidden_from_public_catalogs_and_categories(): void
    {
        Product::create([
            'name' => 'Hidden Gaming Tower',
            'slug' => 'hidden-gaming-tower',
            'type' => 'desktop',
            'category' => 'Hidden Category',
            'price' => 50000,
            'stock_quantity' => 2,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'is_active' => false,
        ]);

        $this->get('/desktops')->assertDontSee('Hidden Gaming Tower');
        $this->get('/categories')->assertDontSee('Hidden Category');
    }

    public function test_component_categories_link_to_accessories(): void
    {
        Product::create([
            'name' => '750W Power Supply',
            'slug' => '750w-power-supply',
            'type' => 'power_supply',
            'category' => 'PSU',
            'price' => 5000,
            'stock_quantity' => 4,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'is_active' => true,
        ]);
        Product::create([
            'name' => 'B550 Motherboard',
            'slug' => 'b550-motherboard',
            'type' => 'motherboard',
            'category' => 'Mother Board',
            'price' => 8000,
            'stock_quantity' => 3,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'is_active' => true,
        ]);
        Product::create([
            'name' => '1TB NVMe Drive',
            'slug' => '1tb-nvme-drive',
            'type' => 'storage',
            'category' => 'Storage',
            'price' => 3000,
            'stock_quantity' => 7,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'is_active' => true,
        ]);

        $response = $this->get('/categories');

        $response->assertSee('/accessories?filter=components');
        $response->assertDontSee('/desktops?filter=gaming');

        $this->get('/accessories?filter=components')
            ->assertSee('750W Power Supply')
            ->assertSee('B550 Motherboard')
            ->assertSee('1TB NVMe Drive');
    }

    public function test_accessory_filters_include_products_with_specific_inventory_types(): void
    {
        Product::create([
            'name' => 'Mechanical Keyboard',
            'slug' => 'mechanical-keyboard-filter-test',
            'type' => 'keyboard',
            'category' => 'Keyboard',
            'price' => 3000,
            'stock_quantity' => 5,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'is_active' => true,
        ]);
        Product::create([
            'name' => '27-inch Monitor',
            'slug' => '27-inch-monitor-filter-test',
            'type' => 'monitor',
            'category' => 'Monitor',
            'price' => 10000,
            'stock_quantity' => 4,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'is_active' => true,
        ]);
        Product::create([
            'name' => 'USB Speakers',
            'slug' => 'usb-speakers-filter-test',
            'type' => 'speaker',
            'category' => 'Speaker',
            'price' => 2000,
            'stock_quantity' => 6,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'is_active' => true,
        ]);

        $this->get('/accessories?filter=peripherals')->assertSee('Mechanical Keyboard');
        $this->get('/accessories?filter=displays')->assertSee('27-inch Monitor');
        $this->get('/accessories?filter=audio')->assertSee('USB Speakers');
    }

    public function test_custom_build_category_links_to_all_desktops(): void
    {
        Product::create([
            'name' => 'Custom PC Build Sample',
            'slug' => 'custom-pc-build-sample',
            'type' => 'desktop',
            'category' => 'Custom Build',
            'price' => 80000,
            'stock_quantity' => 1,
            'low_stock_threshold' => 1,
            'stock_location' => 'store',
            'is_active' => true,
        ]);

        $this->get('/categories')
            ->assertSee('/desktops?filter=all')
            ->assertDontSee('/desktops?filter=gaming');

        $this->get('/desktops?filter=all')->assertSee('Custom PC Build Sample');
    }
}
