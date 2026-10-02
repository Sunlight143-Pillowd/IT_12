<?php

namespace Tests\Feature;

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
