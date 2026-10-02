<?php

namespace Tests\Feature;

use App\Models\PcBuild;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\StockIn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PcBuildWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_serialized_pc_build_sale_records_named_customer_and_unit_on_receipt(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Serial Build CPU',
            'type' => 'cpu',
            'category' => 'CPU',
            'price' => 12000,
            'stock_quantity' => 1,
            'requires_serial' => true,
            'warranty_months' => 36,
        ]);
        $stockIn = StockIn::factory()->create(['employee_id' => $user->id]);
        $stockInItem = $stockIn->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_cost' => 9000,
            'warranty_months' => 36,
        ]);
        $unit = ProductUnit::create([
            'stock_in_item_id' => $stockInItem->id,
            'product_id' => $product->id,
            'serial_number' => 'CPU-BUILD-001',
            'warranty_months' => 36,
            'status' => 'in_stock',
        ]);

        $this->actingAs($user)->post(route('buildpc.store'), [
            'customer_name' => 'Build Sale Customer',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertRedirect(route('buildpc.index'));

        $build = PcBuild::firstOrFail();
        $this->actingAs($user)->post(route('buildpc.sell', $build))->assertRedirect();

        $sale = Sale::with('items.units')->firstOrFail();
        $this->assertSame('Build Sale Customer', $sale->customer_name);
        $this->assertSame('sold', $unit->fresh()->status);
        $this->assertSame($sale->items->first()->id, $unit->fresh()->sale_item_id);

        $this->actingAs($user)->get(route('pos.receipt', $sale))
            ->assertOk()
            ->assertSee('CPU-BUILD-001')
            ->assertSee('36 months');
    }

    public function test_pc_build_requires_a_customer_name(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['type' => 'cpu', 'category' => 'CPU']);

        $this->actingAs($user)->from(route('buildpc.index'))->post(route('buildpc.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertRedirect(route('buildpc.index'))
            ->assertSessionHasErrors('customer_name');

        $this->assertDatabaseCount('pc_builds', 0);
    }
}
