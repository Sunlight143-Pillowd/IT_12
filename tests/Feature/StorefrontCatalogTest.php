<?php

namespace Tests\Feature;

use App\Models\Category;
<<<<<<< HEAD
use App\Models\HomepageImage;
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
<<<<<<< HEAD
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
use Tests\TestCase;

class StorefrontCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_products_are_rendered_on_the_homepage(): void
    {
        $this->seed();

<<<<<<< HEAD
        $this->get('/')
            ->assertSee('Boss Apex 4K')
            ->assertSee('COMPUTER BOSS DAVAO')
            ->assertSee('flex h-52 w-full items-center justify-center')
            ->assertSee('h-full w-full object-contain');
        $this->assertDatabaseHas('categories', ['slug' => 'ready-to-ship']);
    }

    public function test_guest_sign_in_and_register_links_are_visible_in_homepage_header(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<a href="'.route('login').'" class="inline-flex items-center whitespace-nowrap text-xs font-bold uppercase tracking-wide hover:text-purple-700">Sign In</a>', false)
            ->assertSee('<a href="'.route('register').'" class="inline-flex items-center whitespace-nowrap text-xs font-bold uppercase tracking-wide hover:text-purple-700">Register</a>', false);
    }

    public function test_category_page_shows_full_category_photos_without_upload_buttons(): void
    {
        Storage::fake('public');
        $categoryImagePath = 'categories/cpu.png';
        Storage::disk('public')->put(
            $categoryImagePath,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/MioAAAAASUVORK5CYII=')
        );
        Category::create([
            'name' => 'CPU',
            'slug' => 'cpu',
            'image_path' => $categoryImagePath,
        ]);
        Product::create([
            'name' => 'Test CPU',
            'slug' => 'test-cpu',
            'type' => 'cpu',
            'category' => 'CPU',
            'price' => 1000,
            'stock_quantity' => 2,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'is_active' => true,
        ]);

        $this->get(route('store.categories'))
            ->assertOk()
            ->assertSee('CPU')
            ->assertSee(asset('storage/'.$categoryImagePath), false)
            ->assertSee('object-contain')
            ->assertDontSee('Upload Photo')
            ->assertDontSee('Save Photo');

        $employee = User::factory()->create(['email' => 'employee@davaobosscomputer.com']);
        $this->actingAs($employee)
            ->get(route('store.categories'))
            ->assertOk()
            ->assertDontSee('Upload Photo')
            ->assertDontSee('Save Photo');

        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $this->actingAs($admin)
            ->get(route('store.categories'))
            ->assertOk()
            ->assertDontSee('Upload Photo')
            ->assertDontSee('Save Photo');
    }

    public function test_staff_can_upload_a_category_card_photo_and_it_is_shown_on_the_homepage(): void
    {
        Storage::fake('public');
        $staff = User::factory()->create(['email' => 'employee@davaobosscomputer.com']);
        Product::create([
            'name' => 'Test CPU',
            'slug' => 'test-cpu',
            'type' => 'cpu',
            'category' => 'CPU',
            'price' => 1000,
            'stock_quantity' => 2,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'is_active' => true,
        ]);

        $response = $this->actingAs($staff)->post(route('inventory.categories.image', 'cpu'), [
            'category_name' => 'CPU',
            'image' => UploadedFile::fake()->createWithContent(
                'cpu.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/MioAAAAASUVORK5CYII=')
            ),
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('status', 'Photo uploaded for CPU.');

        $category = Category::where('slug', 'cpu')->firstOrFail();
        $this->assertTrue(Storage::disk('public')->exists($category->image_path));

        $this->get('/')
            ->assertOk()
            ->assertSee(asset('storage/'.$category->image_path), false)
            ->assertSee('object-contain')
            ->assertDontSee('Change Photo')
            ->assertDontSee('inventory/categories/cpu/image');
    }

    public function test_non_staff_cannot_upload_a_category_card_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['email' => 'customer@example.com']);

        $this->actingAs($user)
            ->post(route('inventory.categories.image', 'cpu'), [
                'category_name' => 'CPU',
                'image' => UploadedFile::fake()->createWithContent(
                    'cpu.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/MioAAAAASUVORK5CYII=')
                ),
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('categories', ['slug' => 'cpu']);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_staff_can_upload_a_gif_category_card_photo(): void
    {
        Storage::fake('public');
        $staff = User::factory()->create(['email' => 'employee@davaobosscomputer.com']);

        $this->actingAs($staff)
            ->post(route('inventory.categories.image', 'cpu'), [
                'category_name' => 'CPU',
                'image' => UploadedFile::fake()->createWithContent(
                    'cpu.gif',
                    base64_decode('R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=')
                ),
            ])
            ->assertRedirect('/')
            ->assertSessionHasNoErrors();

        $imagePath = Category::where('slug', 'cpu')->value('image_path');
        $this->assertTrue(Storage::disk('public')->exists($imagePath));
    }

    public function test_category_photo_upload_rejects_non_image_files_with_a_clear_message(): void
    {
        Storage::fake('public');
        $staff = User::factory()->create(['email' => 'employee@davaobosscomputer.com']);

        $this->actingAs($staff)
            ->from('/')
            ->post(route('inventory.categories.image', 'cpu'), [
                'category_name' => 'CPU',
                'image' => UploadedFile::fake()->createWithContent('cpu.jpg', 'not an image'),
            ])
            ->assertRedirect('/')
            ->assertSessionHasErrors([
                'image' => 'Please choose a valid JPG, PNG, GIF, BMP, or WEBP image.',
            ]);
    }

    public function test_staff_can_upload_and_preview_a_homepage_hero_photo(): void
    {
        Storage::fake('public');
        $staff = User::factory()->create(['email' => 'employee@davaobosscomputer.com']);
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/MioAAAAASUVORK5CYII=');

        $this->actingAs($staff)
            ->post(route('homepage-images.upload', 'hero-main'), [
                'image' => UploadedFile::fake()->createWithContent('hero.png', $png),
            ])
            ->assertRedirect('/')
            ->assertSessionHas('status', 'Homepage photo uploaded successfully.');

        $homepageImage = HomepageImage::where('key', 'hero-main')->firstOrFail();
        $this->assertTrue(Storage::disk('public')->exists($homepageImage->image_path));

        $this->get('/')
            ->assertOk()
            ->assertSee(asset('storage/'.$homepageImage->image_path), false)
            ->assertSee('Save Photo')
            ->assertDontSee('Change Photo');
    }

    public function test_customers_cannot_upload_homepage_hero_photos(): void
    {
        Storage::fake('public');
        $customer = User::factory()->create(['email' => 'customer@example.com']);

        $this->actingAs($customer)
            ->post(route('homepage-images.upload', 'hero-main'), [
                'image' => UploadedFile::fake()->createWithContent(
                    'hero.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/MioAAAAASUVORK5CYII=')
                ),
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('homepage_images', ['key' => 'hero-main']);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_staff_can_upload_featured_product_photo_and_it_is_shown_on_the_homepage(): void
    {
        Storage::fake('public');
        $staff = User::factory()->create(['email' => 'employee@davaobosscomputer.com']);
        $product = Product::create([
            'name' => 'Featured GPU',
            'slug' => 'featured-gpu',
            'type' => 'gpu',
            'category' => 'GPU',
            'price' => 1000,
            'stock_quantity' => 2,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'is_active' => true,
        ]);

        $this->actingAs($staff)
            ->post(route('inventory.products.image', $product), [
                'image' => UploadedFile::fake()->createWithContent(
                    'gpu.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/MioAAAAASUVORK5CYII=')
                ),
            ])
            ->assertRedirect('/')
            ->assertSessionHas('status', 'Photo uploaded for Featured GPU.');

        $product->refresh();
        $this->assertTrue(Storage::disk('public')->exists($product->image_path));

        $this->get('/')
            ->assertOk()
            ->assertSee(asset('storage/'.$product->image_path), false)
            ->assertDontSee(route('inventory.products.image', $product), false);
    }

    public function test_saved_category_photo_is_used_for_featured_products_without_their_own_photo(): void
    {
        Storage::fake('public');
        $categoryImagePath = 'categories/saved-gpu.png';
        Storage::disk('public')->put(
            $categoryImagePath,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/MioAAAAASUVORK5CYII=')
        );
        Category::create([
            'name' => 'GPU',
            'slug' => 'gpu',
            'image_path' => $categoryImagePath,
        ]);
        Product::create([
            'name' => 'Featured GPU without its own photo',
            'slug' => 'featured-gpu-without-photo',
            'type' => 'gpu',
            'category' => 'GPU',
            'price' => 1000,
            'stock_quantity' => 2,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee(asset('storage/'.$categoryImagePath), false);
    }

    public function test_customers_cannot_upload_featured_product_photos(): void
    {
        Storage::fake('public');
        $customer = User::factory()->create(['email' => 'customer@example.com']);
        $product = Product::create([
            'name' => 'Featured GPU',
            'slug' => 'featured-gpu',
            'type' => 'gpu',
            'category' => 'GPU',
            'price' => 1000,
            'stock_quantity' => 2,
            'low_stock_threshold' => 1,
            'stock_location' => 'warehouse',
            'is_active' => true,
        ]);

        $this->actingAs($customer)
            ->post(route('inventory.products.image', $product), [
                'image' => UploadedFile::fake()->createWithContent(
                    'gpu.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/MioAAAAASUVORK5CYII=')
                ),
            ])
            ->assertForbidden();

        $this->assertNull($product->fresh()->image_path);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

=======
        $this->get('/')->assertSee('Boss Apex 4K');
        $this->assertDatabaseHas('categories', ['slug' => 'ready-to-ship']);
    }

>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
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
