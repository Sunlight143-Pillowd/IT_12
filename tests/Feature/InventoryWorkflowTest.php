<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\StockIn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InventoryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_categories_use_editable_text_fields_and_save_custom_names(): void
    {
        $user = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $category = Category::create([
            'name' => 'Displays',
            'slug' => 'displays',
        ]);
        $product = Product::factory()->create(['category' => 'Displays']);

        $this->actingAs($user)
            ->get(route('inventory.index'))
            ->assertOk()
            ->assertSee('<input type="text" name="name" value="Displays"', false)
            ->assertDontSee('<select name="name"', false)
            ->assertSee('Delete Displays category');

        $this->from(route('inventory.index'))
            ->patch(route('inventory.categories.update', $category), [
                'name' => 'Workstation Parts',
            ])
            ->assertRedirect(route('inventory.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Workstation Parts',
            'slug' => 'workstation-parts',
        ]);
        $this->assertSame('Workstation Parts', $product->fresh()->category);
    }

    public function test_category_can_be_deleted_without_deleting_products_in_that_category(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $imagePath = 'categories/displays.png';
        Storage::disk('public')->put($imagePath, 'category image');
        $category = Category::create([
            'name' => 'Displays',
            'slug' => 'displays',
            'image_path' => $imagePath,
        ]);
        $product = Product::factory()->create(['category' => 'Displays']);

        $this->actingAs($user)
            ->delete(route('inventory.categories.destroy', $category))
            ->assertRedirect(route('inventory.index'))
            ->assertSessionHas('success', 'Category deleted successfully.');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertModelExists($product);
        $this->assertFalse(Storage::disk('public')->exists($imagePath));

        $this->actingAs($user)
            ->get(route('home'))
            ->assertDontSee('DISPLAYS')
            ->assertDontSee('inventory/categories/displays/image');

        $this->get(route('store.categories'))->assertDontSee('Displays');
        $this->get(route('store.category', ['categorySlug' => 'displays']))->assertNotFound();
        $this->get(route('inventory.index'))->assertDontSee('<option value="Displays"', false);

        $this->actingAs($user)
            ->post(route('inventory.categories.image', 'displays'))
            ->assertNotFound();

        $this->assertDatabaseMissing('categories', ['slug' => 'displays']);
    }

    public function test_category_text_field_rejects_duplicate_category_names(): void
    {
        $user = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $category = Category::create(['name' => 'Displays', 'slug' => 'displays']);
        Category::create(['name' => 'Gaming', 'slug' => 'gaming']);

        $this->actingAs($user)
            ->from(route('inventory.index'))
            ->patch(route('inventory.categories.update', $category), [
                'name' => 'Gaming',
            ])
            ->assertRedirect(route('inventory.index'))
            ->assertSessionHasErrors('name');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Displays',
            'slug' => 'displays',
        ]);
    }

    public function test_staff_can_reorder_categories_and_customers_see_the_saved_order(): void
    {
        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $regularUser = User::factory()->create();
        $alpha = Category::create(['name' => 'Alpha Parts', 'slug' => 'alpha-parts']);
        $beta = Category::create(['name' => 'Beta Parts', 'slug' => 'beta-parts']);
        Product::factory()->create(['category' => $alpha->name, 'stock_quantity' => 3]);
        Product::factory()->create(['category' => $beta->name, 'stock_quantity' => 3]);

        $this->actingAs($regularUser)
            ->post(route('inventory.categories.reorder'), ['category_ids' => [$beta->id, $alpha->id]])
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('inventory.index'))
            ->assertSee('draggable="true"', false)
            ->assertSee('Save category order');

        $this->actingAs($admin)
            ->post(route('inventory.categories.reorder'), ['category_ids' => [$beta->id, $alpha->id]])
            ->assertRedirect(route('inventory.index'))
            ->assertSessionHas('success', 'Category order saved.');

        $this->assertSame(0, $beta->fresh()->sort_order);
        $this->assertSame(1, $alpha->fresh()->sort_order);

        $catalog = $this->get(route('store.categories'))->assertOk()->getContent();
        $this->assertLessThan(
            strpos($catalog, 'Alpha Parts'),
            strpos($catalog, 'Beta Parts')
        );
    }

    public function test_inventory_stock_sorting_respects_search_and_low_stock_status(): void
    {
        $user = User::factory()->create();
        Product::factory()->create([
            'name' => 'Stock Sort Low',
            'category' => 'Sorting Test',
            'stock_quantity' => 1,
            'low_stock_threshold' => 2,
        ]);
        Product::factory()->create([
            'name' => 'Stock Sort High',
            'category' => 'Sorting Test',
            'stock_quantity' => 8,
            'low_stock_threshold' => 2,
        ]);
        Product::factory()->create([
            'name' => 'Unrelated Product',
            'stock_quantity' => 0,
            'low_stock_threshold' => 0,
        ]);

        $ascending = $this->actingAs($user)
            ->get(route('inventory.index', ['search' => 'Stock Sort', 'stock_sort' => 'ascending']))
            ->assertSee('Stock Sort Low')
            ->assertSee('Stock Sort High')
            ->assertSee('LOW STOCK')
            ->assertDontSee('Unrelated Product')
            ->getContent();
        $this->assertLessThan(strpos($ascending, 'Stock Sort High'), strpos($ascending, 'Stock Sort Low'));

        $descending = $this->get(route('inventory.index', ['search' => 'Stock Sort', 'stock_sort' => 'descending']))->getContent();
        $this->assertLessThan(strpos($descending, 'Stock Sort High'), strpos($descending, 'Stock Sort Low'));
    }

    public function test_supplier_delivery_uploads_proof_records_units_and_increments_stock(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Serial Keyboard',
            'requires_serial' => true,
            'warranty_months' => 24,
        ]);

        $response = $this->actingAs($user)->post(route('stock-in.store'), [
            'supplier_name' => 'Example Components',
            'supplier_contact' => '09170000000',
            'invoice_number' => 'INV-1001',
            'received_at' => '2026-10-01',
            'delivery_document' => UploadedFile::fake()->create('invoice.pdf', 100, 'application/pdf'),
            'before_photo' => UploadedFile::fake()->create('before.jpg', 100, 'image/jpeg'),
            'after_photo' => UploadedFile::fake()->create('after.jpg', 100, 'image/jpeg'),
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 2,
                'unit_cost' => 1200,
                'warranty_months' => 24,
                'serial_numbers' => "KB-1001\nKB-1002",
            ]],
        ]);

        $response->assertRedirect(route('stock-in.index'));
        $response->assertSessionHasNoErrors();
        $this->assertSame(2, $product->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_ins', [
            'supplier_name' => 'Example Components',
            'invoice_number' => 'INV-1001',
            'employee_id' => $user->id,
        ]);
        $this->assertDatabaseHas('stock_in_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_cost' => 1200,
        ]);
        $this->assertDatabaseHas('product_units', [
            'product_id' => $product->id,
            'serial_number' => 'KB-1001',
            'warranty_months' => 24,
            'status' => 'in_stock',
        ]);
        $this->assertSame(2, ProductUnit::where('product_id', $product->id)->count());
        $stockIn = StockIn::firstOrFail();
        Storage::disk('public')->assertExists($stockIn->delivery_document_path);
        Storage::disk('public')->assertExists($stockIn->before_photo_path);
        Storage::disk('public')->assertExists($stockIn->after_photo_path);
    }

    public function test_serial_count_mismatch_does_not_record_delivery_or_change_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['requires_serial' => true]);

        $response = $this->actingAs($user)->from(route('stock-in.index'))->post(route('stock-in.store'), [
            'supplier_name' => 'Example Components',
            'invoice_number' => 'INV-1002',
            'received_at' => '2026-10-01',
            'delivery_document' => UploadedFile::fake()->create('invoice.pdf', 100, 'application/pdf'),
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 2,
                'unit_cost' => 1200,
                'serial_numbers' => 'KB-2001',
            ]],
        ]);

        $response->assertRedirect(route('stock-in.index'));
        $response->assertSessionHasErrors('items.0.serial_numbers');
        $this->assertSame(0, $product->fresh()->stock_quantity);
        $this->assertDatabaseCount('stock_ins', 0);
        $this->assertDatabaseCount('product_units', 0);
    }

    public function test_stock_in_delivery_can_be_edited_and_reconciles_stock_and_serial_units(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Serial Keyboard',
            'requires_serial' => true,
            'warranty_months' => 24,
        ]);
        $this->actingAs($user)->post(route('stock-in.store'), [
            'supplier_name' => 'Example Components',
            'invoice_number' => 'INV-EDIT-1',
            'received_at' => '2026-10-01',
            'delivery_document' => UploadedFile::fake()->create('invoice.pdf', 100, 'application/pdf'),
            'items' => [[
                'product_id' => $product->id,
                'quantity' => 2,
                'unit_cost' => 1200,
                'warranty_months' => 24,
                'serial_numbers' => "KB-1001\nKB-1002",
            ]],
        ])->assertRedirect(route('stock-in.index'));
        $stockIn = StockIn::firstOrFail();

        $this->get(route('stock-in.edit', $stockIn))
            ->assertOk()
            ->assertSee('Edit supplier delivery')
            ->assertSee('KB-1001')
            ->assertSee('read-only')
            ->assertDontSee('name="items[', false);

        $this->patch(route('stock-in.update', $stockIn), [
            'supplier_name' => 'Updated Components',
            'supplier_contact' => '09170000000',
            'invoice_number' => 'INV-EDIT-2',
            'received_at' => '2026-10-02',
            'notes' => 'Corrected delivery details',
        ])->assertRedirect(route('stock-in.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(2, $product->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_ins', [
            'id' => $stockIn->id,
            'supplier_name' => 'Updated Components',
            'invoice_number' => 'INV-EDIT-2',
            'notes' => 'Corrected delivery details',
        ]);
        $this->assertDatabaseHas('stock_in_items', [
            'stock_in_id' => $stockIn->id,
            'quantity' => 2,
            'unit_cost' => 1200,
            'warranty_months' => 24,
        ]);
        $this->assertDatabaseHas('product_units', [
            'product_id' => $product->id,
            'serial_number' => 'KB-1001',
            'warranty_months' => 24,
            'status' => 'in_stock',
        ]);
        $this->assertDatabaseHas('product_units', [
            'product_id' => $product->id,
            'serial_number' => 'KB-1002',
            'warranty_months' => 24,
            'status' => 'in_stock',
        ]);
        $this->assertDatabaseCount('product_units', 2);

        $this->from(route('stock-in.edit', $stockIn))
            ->patch(route('stock-in.update', $stockIn), [
                'supplier_name' => 'Updated Components',
                'invoice_number' => 'INV-EDIT-2',
                'received_at' => '2026-10-02',
                'items' => [[
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_cost' => 1500,
                    'warranty_months' => 18,
                    'serial_numbers' => 'KB-1002',
                ]],
            ])
            ->assertRedirect(route('stock-in.edit', $stockIn))
            ->assertSessionHasErrors('items');

        $this->assertSame(2, $product->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_in_items', [
            'stock_in_id' => $stockIn->id,
            'quantity' => 2,
            'unit_cost' => 1200,
            'warranty_months' => 24,
        ]);
        $this->assertDatabaseCount('product_units', 2);
    }

    public function test_delivery_with_sold_units_allows_metadata_edits_but_locks_item_changes(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['requires_serial' => true, 'stock_quantity' => 1]);
        $stockIn = StockIn::factory()->create(['employee_id' => $user->id]);
        $item = $stockIn->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_cost' => 8000,
            'warranty_months' => 36,
        ]);
        ProductUnit::create([
            'stock_in_item_id' => $item->id,
            'product_id' => $product->id,
            'serial_number' => 'SOLD-UNIT-1',
            'warranty_months' => 36,
            'status' => 'sold',
        ]);

        $this->actingAs($user)
            ->get(route('stock-in.edit', $stockIn))
            ->assertOk()
            ->assertSee('Delivered products, quantities, unit costs, warranties, and serial numbers are read-only.');

        $this->patch(route('stock-in.update', $stockIn), [
            'supplier_name' => $stockIn->supplier_name,
            'supplier_contact' => $stockIn->supplier_contact,
            'invoice_number' => $stockIn->invoice_number,
            'received_at' => $stockIn->received_at->toDateString(),
            'notes' => 'Metadata is still editable',
        ])->assertRedirect(route('stock-in.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('stock_ins', [
            'id' => $stockIn->id,
            'notes' => 'Metadata is still editable',
        ]);
        $this->assertDatabaseHas('stock_in_items', ['id' => $item->id, 'quantity' => 1]);
        $this->assertDatabaseHas('product_units', ['serial_number' => 'SOLD-UNIT-1', 'status' => 'sold']);

        $this->from(route('stock-in.edit', $stockIn))
            ->patch(route('stock-in.update', $stockIn), [
                'supplier_name' => $stockIn->supplier_name,
                'invoice_number' => $stockIn->invoice_number,
                'received_at' => $stockIn->received_at->toDateString(),
                'items' => [],
            ])->assertRedirect(route('stock-in.edit', $stockIn))
            ->assertSessionHasErrors('items');

        $this->assertDatabaseHas('stock_in_items', ['id' => $item->id, 'quantity' => 1]);
    }

    public function test_inventory_product_can_be_edited_with_product_and_before_after_photos(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Old Keyboard',
            'stock_quantity' => 4,
        ]);

        $this->actingAs($user)
            ->patch(route('inventory.products.update', $product), [
                'name' => 'Updated Keyboard',
                'type' => 'keyboard',
                'category' => 'Peripherals',
                'price' => 2500,
                'stock_location' => 'store',
                'low_stock_threshold' => 2,
                'description' => 'Updated description',
                'requires_serial' => '1',
                'warranty_months' => 24,
                'image' => UploadedFile::fake()->create('product.jpg', 100, 'image/jpeg'),
                'before_image' => UploadedFile::fake()->create('before.jpg', 100, 'image/jpeg'),
                'after_image' => UploadedFile::fake()->create('after.jpg', 100, 'image/jpeg'),
            ])
            ->assertRedirect(route('inventory.index'))
            ->assertSessionHasNoErrors();

        $product->refresh();
        $this->assertSame('Updated Keyboard', $product->name);
        $this->assertSame(4, $product->stock_quantity);
        $this->assertTrue($product->requires_serial);
        $this->assertSame(24, $product->warranty_months);
        Storage::disk('public')->assertExists($product->image_path);
        Storage::disk('public')->assertExists($product->before_image_path);
        Storage::disk('public')->assertExists($product->after_image_path);
    }

    public function test_pos_sale_assigns_the_selected_unit_and_receipt_shows_serial_and_warranty(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Serialized Monitor',
            'requires_serial' => true,
            'warranty_months' => 36,
            'stock_quantity' => 1,
        ]);
        $stockIn = StockIn::factory()->create(['employee_id' => $user->id]);
        $stockInItem = $stockIn->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_cost' => 8000,
            'warranty_months' => 36,
        ]);
        $unit = ProductUnit::create([
            'stock_in_item_id' => $stockInItem->id,
            'product_id' => $product->id,
            'serial_number' => 'MON-36001',
            'warranty_months' => 36,
            'status' => 'in_stock',
        ]);

        $this->actingAs($user)->post(route('pos.checkout'), [
            'customer_name' => 'Named Customer',
            'cart_json' => json_encode([[
                'product_id' => $product->id,
                'quantity' => 1,
                'serial_numbers' => ['MON-36001'],
            ]]),
        ])->assertRedirect(route('pos.index'));

        $sale = Sale::with('items.units')->firstOrFail();
        $this->assertSame('Named Customer', $sale->customer_name);
        $this->assertSame('sold', $unit->fresh()->status);
        $this->assertSame($sale->items->first()->id, $unit->fresh()->sale_item_id);
        $this->assertSame(0, $product->fresh()->stock_quantity);

        $receipt = $this->actingAs($user)->get(route('pos.index'));
        $receipt->assertOk();
        $receipt->assertSee('MON-36001');
        $receipt->assertSee('36 months');
    }

    public function test_pos_rejects_a_sale_without_a_customer_name(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 1]);

        $this->actingAs($user)->from(route('pos.index'))->post(route('pos.checkout'), [
            'cart_json' => json_encode([[
                'product_id' => $product->id,
                'quantity' => 1,
            ]]),
        ])->assertRedirect(route('pos.index'))
            ->assertSessionHasErrors('customer_name');

        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(1, $product->fresh()->stock_quantity);
    }
}
