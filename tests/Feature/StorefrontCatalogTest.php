<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\HomepageImage;
use App\Models\PcBuild;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class StorefrontCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_products_and_photos_are_rendered_in_the_storefront(): void
    {
        $this->seed();

        $seededProducts = Product::query()
            ->whereIn('slug', [
                'boss-strike-itx',
                'boss-vanguard-mid',
                'boss-apex-4k',
                'boss-nomad-15',
                'boss-overclock-16',
                'boss-mechanical-keyboard',
                'boss-wireless-mouse',
                '27-1440p-monitor',
            ])
            ->get();

        $this->assertCount(8, $seededProducts);

        foreach ($seededProducts as $product) {
            $this->assertNotEmpty($product->image_path);
            Storage::disk('public')->assertExists($product->image_path);
        }

        $this->get('/')
            ->assertSee('Boss Wireless Mouse')
            ->assertSee('Special offer')
            ->assertSee('COMPUTER BOSS DAVAO')
            ->assertSee('data-product-carousel', false)
            ->assertSee('aria-roledescription="carousel"', false)
            ->assertDontSee('Product image coming soon');

        $this->get(route('store.desktops'))
            ->assertDontSee('Photo unavailable')
            ->assertSee(asset('storage/products/3N2lr3q3LeDrWS8NQoGsPHmtcMC84Ji69Gk3q0LH.jpg'), false);

        $this->get(route('store.accessories'))
            ->assertDontSee('Photo unavailable')
            ->assertSee(asset('storage/products/GukOLwKANPFr38OfF8QMj11Pv62LUKK49ochNKW1.jpg'), false);

        $this->assertDatabaseHas('categories', ['slug' => 'ready-to-ship']);
    }

    public function test_seeding_does_not_replace_an_existing_product_photo(): void
    {
        $this->seed();

        $product = Product::where('slug', 'boss-strike-itx')->firstOrFail();
        $product->update(['image_path' => 'products/custom-upload.jpg']);

        $this->seed();

        $this->assertSame('products/custom-upload.jpg', $product->fresh()->image_path);
    }

    public function test_guest_sign_in_and_register_links_are_visible_in_homepage_header(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('data-product-carousel', false)
            ->assertSee('aria-roledescription="carousel"', false)
            ->assertSee('aria-label="Product carousel"', false)
            ->assertDontSee('Toggle product navigation')
            ->assertSee('aria-label="Product navigation"', false)
            ->assertSee('flex-wrap items-center gap-x-4 gap-y-2 border-t border-gray-100 py-2', false)
            ->assertSee('START NOW')
            ->assertDontSee('Computer Boss Davao: your go-to shop for gaming PCs, parts, and accessories.')
            ->assertDontSee('Add to cart')
            ->assertDontSee('Featured Products')
            ->assertDontSee('Featured product')
            ->assertDontSee('<<<<<<< HEAD')
            ->assertDontSee('=======')
            ->assertDontSee('>>>>>>>')
            ->assertSee('<a href="'.route('login').'" class="inline-flex items-center whitespace-nowrap text-xs font-bold uppercase tracking-wide hover:text-purple-700">Sign In</a>', false)
            ->assertSee('<a href="'.route('register').'" class="inline-flex items-center whitespace-nowrap text-xs font-bold uppercase tracking-wide hover:text-purple-700">Register</a>', false);

        $matchedProductNavigation = preg_match('/<nav aria-label="Product navigation"[^>]*>(.*?)<\/nav>/s', $response->getContent(), $productNavigation);

        $this->assertSame(1, $matchedProductNavigation);
        $this->assertArrayHasKey(1, $productNavigation);
        $this->assertStringContainsString('DESKTOPS', $productNavigation[1]);
        $this->assertStringContainsString('OFFERS', $productNavigation[1]);
        $this->assertStringNotContainsString('Sign in', $productNavigation[1]);
        $this->assertStringNotContainsString('Register', $productNavigation[1]);
        $this->assertStringNotContainsString('Dashboard', $productNavigation[1]);
        $this->assertStringContainsString('BUILD PC', $productNavigation[1]);
        $this->assertStringNotContainsString('Cart', $productNavigation[1]);
    }

    public function test_guest_can_open_customer_build_page_from_homepage_navigation(): void
    {
        $this->get(route('home'))
            ->assertSee('<a href="'.route('buildpc.customer').'" class="shrink-0 whitespace-nowrap hover:text-purple-700">BUILD PC</a>', false);

        $this->get(route('buildpc.customer'))
            ->assertOk()
            ->assertSee('Build your PC')
            ->assertSee('Submit build request');
    }

    public function test_homepage_category_navigation_dropdown_lists_all_categories_in_saved_order(): void
    {
        Category::create([
            'name' => 'Monitors',
            'slug' => 'monitors',
            'sort_order' => 2,
        ]);
        Category::create([
            'name' => 'Graphics Cards',
            'slug' => 'graphics-cards',
            'sort_order' => 1,
        ]);

        $content = $this->get(route('home'))
            ->assertOk()
            ->assertSee('group-hover:visible', false)
            ->assertSee('group-focus-within:visible', false)
            ->assertSee('href="'.route('store.category', ['categorySlug' => 'graphics-cards']).'"', false)
            ->assertSee('href="'.route('store.category', ['categorySlug' => 'monitors']).'"', false)
            ->getContent();

        $graphicsPosition = strpos($content, 'Graphics Cards');
        $monitorsPosition = strpos($content, 'Monitors');

        $this->assertNotFalse($graphicsPosition);
        $this->assertGreaterThan($graphicsPosition, $monitorsPosition);
    }

    public function test_catalog_does_not_render_merge_conflict_markers(): void
    {
        Product::factory()->create([
            'name' => 'Conflict-free Gaming Desktop',
            'type' => 'desktop',
            'stock_quantity' => 2,
        ]);

        $this->get(route('store.desktops'))
            ->assertSee('Conflict-free Gaming Desktop')
            ->assertDontSee('<<<<<<< HEAD')
            ->assertDontSee('=======')
            ->assertDontSee('>>>>>>>');
    }

    public function test_desktop_catalog_hides_ready_to_ship_filter_chip_but_filter_route_still_works(): void
    {
        Product::factory()->create([
            'name' => 'Ready Desktop Filter Test',
            'type' => 'desktop',
            'category' => 'Ready to Ship',
            'stock_quantity' => 2,
        ]);
        Product::factory()->create([
            'name' => 'Gaming Desktop Filter Test',
            'type' => 'desktop',
            'category' => 'Gaming',
            'stock_quantity' => 2,
        ]);

        $this->get(route('store.desktops'))
            ->assertDontSee('Ready to Ship');

        $this->get(route('store.desktops', ['filter' => 'ready-to-ship']))
            ->assertSee('Ready Desktop Filter Test')
            ->assertDontSee('Gaming Desktop Filter Test');
    }

    public function test_homepage_hero_carousel_shows_in_stock_special_offers_with_navigation(): void
    {
        Product::factory()->create([
            'name' => 'Carousel Gaming PC',
            'description' => 'A complete gaming setup for smooth play.',
            'price' => 25000,
            'stock_quantity' => 3,
            'is_active' => true,
        ]);
        Product::factory()->create([
            'name' => 'Carousel Graphics Card',
            'description' => 'Fast graphics for high-resolution gaming.',
            'price' => 15000,
            'stock_quantity' => 2,
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-product-carousel', false)
            ->assertSee('Carousel Gaming PC')
            ->assertSee('A complete gaming setup for smooth play.')
            ->assertSee('Carousel Graphics Card')
            ->assertSee('Fast graphics for high-resolution gaming.')
            ->assertSee('Previous special offer')
            ->assertSee('Next special offer')
            ->assertSee('Show Carousel Gaming PC')
            ->assertSee('Show Carousel Graphics Card')
            ->assertSee('slides[activeSlide]?.name', false)
            ->assertSee('slides[activeSlide]?.description', false)
            ->assertSee('Special offer')
            ->assertDontSee('Add to cart')
            ->assertDontSee('Featured product');
    }

    public function test_products_with_no_available_stock_are_hidden_from_storefront_and_build_selection(): void
    {
        $outOfStockDesktop = Product::factory()->create([
            'name' => 'Out of Stock Gaming Tower',
            'type' => 'desktop',
            'category' => 'Sold Out Gaming',
            'stock_quantity' => 0,
        ]);
        Product::factory()->create([
            'name' => 'Out of Stock Notebook',
            'type' => 'laptop',
            'category' => 'Sold Out Notebooks',
            'stock_quantity' => 0,
        ]);
        Product::factory()->create([
            'name' => 'Out of Stock Mouse',
            'type' => 'mouse',
            'category' => 'Sold Out Peripherals',
            'stock_quantity' => 0,
        ]);
        $employee = User::factory()->create();
        $sale = Sale::create([
            'employee_id' => $employee->id,
            'customer_name' => 'Stock Visibility Customer',
            'total_amount' => 1000,
        ]);
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $outOfStockDesktop->id,
            'product_name' => $outOfStockDesktop->name,
            'unit_price' => 1000,
            'quantity' => 1,
            'subtotal' => 1000,
        ]);
        $reservedProduct = Product::factory()->create([
            'name' => 'Reserved Out of Stock Graphics Card',
            'type' => 'gpu',
            'category' => 'Sold Out Components',
            'stock_quantity' => 1,
        ]);
        $build = PcBuild::create([
            'build_number' => 'PC-STOCK-HIDE-1',
            'customer_name' => 'Stock Visibility Test',
        ]);
        $reservedProduct->reservations()->create([
            'pc_build_id' => $build->id,
            'quantity' => 1,
            'status' => 'active',
        ]);
        Product::factory()->create([
            'name' => 'Available Gaming Tower',
            'type' => 'desktop',
            'category' => 'Gaming',
            'stock_quantity' => 2,
        ]);

        $this->get(route('home'))
            ->assertSee('Available Gaming Tower')
            ->assertDontSee('Out of Stock Gaming Tower')
            ->assertDontSee('Out of Stock Notebook')
            ->assertDontSee('Out of Stock Mouse')
            ->assertDontSee('Reserved Out of Stock Graphics Card');
        $this->get(route('store.desktops'))
            ->assertSee('Available Gaming Tower')
            ->assertDontSee('Out of Stock Gaming Tower');
        $this->get(route('store.laptops'))->assertDontSee('Out of Stock Notebook');
        $this->get(route('store.accessories'))->assertDontSee('Out of Stock Mouse');
        $this->get(route('store.categories'))
            ->assertDontSee('Sold Out Gaming')
            ->assertDontSee('Sold Out Notebooks')
            ->assertDontSee('Sold Out Peripherals')
            ->assertDontSee('Sold Out Components');
        $this->get(route('store.special-offers'))
            ->assertSee('Available Gaming Tower')
            ->assertDontSee('Out of Stock Gaming Tower');
        $this->get(route('store.top-selling'))->assertDontSee('Out of Stock Gaming Tower');
        $this->get(route('buildpc.customer'))
            ->assertDontSee('Reserved Out of Stock Graphics Card');

        $this->assertModelExists($outOfStockDesktop);
    }

    public function test_guest_catalog_keeps_page_content_inside_the_layout_container(): void
    {
        $response = $this->get(route('store.desktops'));
        $document = new \DOMDocument;
        $previousLibxmlErrorHandling = libxml_use_internal_errors(true);
        $document->loadHTML((string) $response->getContent());
        libxml_clear_errors();
        libxml_use_internal_errors($previousLibxmlErrorHandling);
        $xpath = new \DOMXPath($document);
        $layout = $xpath->query('//body/div[contains(concat(" ", normalize-space(@class), " "), " min-h-screen ")]')->item(0);

        $response->assertOk();
        $this->assertNotNull($layout);
        $this->assertSame($layout, $xpath->query('//body/div[contains(concat(" ", normalize-space(@class), " "), " min-h-screen ")]/nav')->item(0)?->parentNode);
        $this->assertSame($layout, $xpath->query('//body/div[contains(concat(" ", normalize-space(@class), " "), " min-h-screen ")]/header')->item(0)?->parentNode);
        $this->assertSame($layout, $xpath->query('//body/div[contains(concat(" ", normalize-space(@class), " "), " min-h-screen ")]/main')->item(0)?->parentNode);
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

    public function test_category_navigation_dropdown_shows_all_categories_in_saved_order(): void
    {
        Category::create([
            'name' => 'Monitors',
            'slug' => 'monitors',
            'sort_order' => 2,
        ]);
        Category::create([
            'name' => 'Graphics Cards',
            'slug' => 'graphics-cards',
            'sort_order' => 1,
        ]);

        $content = $this->get(route('store.desktops'))
            ->assertOk()
            ->assertSee('group-hover:visible', false)
            ->assertSee('group-focus-within:visible', false)
            ->assertSee('href="'.route('store.category', ['categorySlug' => 'graphics-cards']).'"', false)
            ->assertSee('href="'.route('store.category', ['categorySlug' => 'monitors']).'"', false)
            ->getContent();

        $graphicsPosition = strpos($content, 'Graphics Cards');
        $monitorsPosition = strpos($content, 'Monitors');

        $this->assertNotFalse($graphicsPosition);
        $this->assertGreaterThan($graphicsPosition, $monitorsPosition);
    }

    public function test_staff_can_upload_a_category_card_photo_and_it_is_shown_on_the_homepage(): void
    {
        Storage::fake('public');
        $staff = User::factory()->create(['email' => 'employee@davaobosscomputer.com']);
        Category::create(['name' => 'CPU', 'slug' => 'cpu']);

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
        Category::create(['name' => 'CPU', 'slug' => 'cpu']);

        $this->actingAs($user)
            ->post(route('inventory.categories.image', 'cpu'), [
                'image' => UploadedFile::fake()->createWithContent(
                    'cpu.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/MioAAAAASUVORK5CYII=')
                ),
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('categories', ['slug' => 'cpu', 'image_path' => null]);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_staff_can_upload_a_gif_category_card_photo(): void
    {
        Storage::fake('public');
        $staff = User::factory()->create(['email' => 'employee@davaobosscomputer.com']);
        Category::create(['name' => 'CPU', 'slug' => 'cpu']);

        $this->actingAs($staff)
            ->post(route('inventory.categories.image', 'cpu'), [
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
        Category::create(['name' => 'CPU', 'slug' => 'cpu']);

        $this->actingAs($staff)
            ->from('/')
            ->post(route('inventory.categories.image', 'cpu'), [
                'image' => UploadedFile::fake()->createWithContent('cpu.jpg', 'not an image'),
            ])
            ->assertRedirect('/')
            ->assertSessionHasErrors([
                'image' => 'Please choose a valid JPG, PNG, GIF, BMP, or WEBP image.',
            ]);
    }

    public function test_legacy_homepage_photo_upload_does_not_replace_the_product_carousel(): void
    {
        Storage::fake('public');
        $staff = User::factory()->create(['email' => 'employee@davaobosscomputer.com']);
        Product::factory()->create([
            'name' => 'Carousel PC',
            'stock_quantity' => 1,
            'is_active' => true,
        ]);
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
            ->assertSee('data-product-carousel', false)
            ->assertSee('Carousel PC')
            ->assertDontSee(asset('storage/'.$homepageImage->image_path), false);
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

    public function test_product_categories_link_to_exact_category_pages(): void
    {
        foreach (['PSU', 'Mother Board', 'Storage'] as $categoryName) {
            Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
            ]);
        }

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

        $response->assertSee('/categories/psu');
        $response->assertSee('/categories/mother-board');
        $response->assertSee('/categories/storage');
        $response->assertDontSee('/desktops?filter=gaming');

        $this->get('/categories/psu')
            ->assertSee('750W Power Supply')
            ->assertSee('Back to Categories')
            ->assertSee(route('store.categories'), false)
            ->assertSee('border border-purple-600 bg-white px-4 py-2 text-sm font-semibold text-purple-700', false)
            ->assertDontSee('B550 Motherboard')
            ->assertDontSee('1TB NVMe Drive');
        $this->get('/categories/mother-board')
            ->assertSee('B550 Motherboard')
            ->assertDontSee('750W Power Supply')
            ->assertDontSee('1TB NVMe Drive');
        $this->get('/categories/storage')
            ->assertSee('1TB NVMe Drive')
            ->assertDontSee('750W Power Supply')
            ->assertDontSee('B550 Motherboard');
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

    public function test_custom_build_category_links_to_its_exact_category_page(): void
    {
        Category::create(['name' => 'Custom Build', 'slug' => 'custom-build']);

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
            ->assertSee('/categories/custom-build')
            ->assertDontSee('/desktops?filter=all');

        $this->get('/categories/custom-build')->assertSee('Custom PC Build Sample');
    }
}
