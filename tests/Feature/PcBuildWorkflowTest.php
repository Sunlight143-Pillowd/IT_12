<?php

namespace Tests\Feature;

use App\Models\PcBuild;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\StockIn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_staff_build_automatically_uses_one_of_each_selected_component(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'type' => 'cpu',
            'category' => 'CPU',
            'price' => 12000,
            'stock_quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('buildpc.index'))
            ->assertOk()
            ->assertSee('id="build-summary-body"', false)
            ->assertSee("componentSelects.forEach((select) => select.addEventListener('change', updateBuildSummary));", false)
            ->assertDontSee('name="items[cpu][quantity]"')
            ->assertDontSee('>Qty<', false);

        $this->actingAs($user)->post(route('buildpc.store'), [
            'customer_name' => 'Automatic Quantity Customer',
            'items' => [['product_id' => $product->id, 'quantity' => 10]],
        ])->assertRedirect(route('buildpc.index'));

        $build = PcBuild::firstOrFail();
        $this->assertDatabaseHas('pc_build_items', [
            'pc_build_id' => $build->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'subtotal' => 12000,
        ]);
        $this->assertSame(12000.0, (float) $build->total_cost);
    }

    public function test_customer_can_submit_build_and_track_status_and_uploaded_photos(): void
    {
        Storage::fake('public');
        $customer = User::factory()->create(['name' => 'Build Customer']);
        $cpu = Product::factory()->create([
            'name' => 'Build CPU',
            'type' => 'cpu',
            'category' => 'CPU',
            'price' => 12000,
            'stock_quantity' => 2,
        ]);
        $ram = Product::factory()->create([
            'name' => 'Build RAM',
            'type' => 'ram',
            'category' => 'RAM',
            'price' => 3000,
            'stock_quantity' => 4,
        ]);

        $this->actingAs($customer)
            ->get(route('buildpc.customer'))
            ->assertOk()
            ->assertSee('Build your PC')
            ->assertSee('Build Customer')
            ->assertSee($customer->email)
            ->assertSee('Build CPU')
            ->assertSee('Estimated total')
            ->assertSee('id="build-summary-body"', false)
            ->assertSee("componentSelects.forEach((select) => select.addEventListener('change', updateBuildSummary));", false)
            ->assertDontSee('name="items[cpu][quantity]"')
            ->assertDontSee('>Qty<', false);

        $this->actingAs($customer)->post(route('buildpc.customer.store'), [
            'items' => [
                'cpu' => ['product_id' => $cpu->id, 'quantity' => 8],
                'ram' => ['product_id' => $ram->id, 'quantity' => 2],
            ],
        ])->assertRedirect(route('buildpc.customer'));

        $build = PcBuild::query()->where('user_id', $customer->id)->firstOrFail();
        $this->assertDatabaseHas('pc_builds', [
            'id' => $build->id,
            'customer_name' => 'Build Customer',
            'status' => 'pending',
            'total_cost' => 15000,
        ]);
        $this->assertDatabaseMissing('products', ['category' => 'Custom Build']);
        $this->assertSame([1, 1], $build->items()->orderBy('id')->pluck('quantity')->all());
        $this->assertSame(2, $cpu->fresh()->stock_quantity);
        $this->assertSame(4, $ram->fresh()->stock_quantity);

        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertDontSee('Customer Build / PC Build Management');

        $this->actingAs($admin)
            ->get(route('buildpc.index'))
            ->assertSee('Customer Build / PC Build Management')
            ->assertSee($build->build_number)
            ->assertSee('Build CPU');

        $this->actingAs($admin)
            ->from(route('buildpc.index'))
            ->patch(route('dashboard.customer-builds.update', $build), [
                'status' => 'accepted',
                'product_photo' => UploadedFile::fake()->create('build-product.png', 10, 'image/png'),
                'before_photo' => UploadedFile::fake()->create('build-before.png', 10, 'image/png'),
            ])
            ->assertRedirect(route('buildpc.index'));

        $build->refresh();
        $this->assertSame('accepted', $build->status);
        $this->assertNotNull($build->stock_deducted_at);
        $this->assertNotNull($build->product_photo_path);
        $this->assertNotNull($build->before_photo_path);
        Storage::disk('public')->assertExists($build->product_photo_path);
        Storage::disk('public')->assertExists($build->before_photo_path);
        $this->assertSame(1, $cpu->fresh()->stock_quantity);
        $this->assertSame(3, $ram->fresh()->stock_quantity);

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertSee('My PC Builds')
            ->assertSee($build->build_number)
            ->assertSee('Accepted')
            ->assertSee('Build CPU')
            ->assertSee('Product photo');

        foreach (['building', 'testing', 'ready', 'completed'] as $status) {
            $this->actingAs($admin)
                ->patch(route('dashboard.customer-builds.update', $build), ['status' => $status])
                ->assertRedirect();
        }

        $this->assertDatabaseHas('pc_builds', ['id' => $build->id, 'status' => 'completed']);
    }

    public function test_customer_cannot_change_another_customers_build(): void
    {
        $owner = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $build = PcBuild::create([
            'build_number' => 'PC-OWNER-0001',
            'customer_name' => $owner->name,
            'customer_email' => $owner->email,
            'user_id' => $owner->id,
            'status' => 'pending',
            'total_cost' => 1000,
        ]);

        $this->actingAs($otherCustomer)
            ->patch(route('dashboard.customer-builds.update', $build), ['status' => 'accepted'])
            ->assertForbidden();

        $this->assertDatabaseHas('pc_builds', ['id' => $build->id, 'status' => 'pending']);
    }

    public function test_customer_build_cancellation_restores_stock_deducted_on_acceptance(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create([
            'type' => 'cpu',
            'category' => 'CPU',
            'stock_quantity' => 1,
        ]);
        $build = PcBuild::create([
            'build_number' => 'PC-CANCEL-0001',
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'user_id' => $customer->id,
            'status' => 'pending',
            'total_cost' => 1000,
        ]);
        $build->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 1000,
            'subtotal' => 1000,
        ]);
        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);

        $this->actingAs($admin)->patch(route('dashboard.customer-builds.update', $build), ['status' => 'accepted']);
        $this->actingAs($admin)->patch(route('dashboard.customer-builds.update', $build), ['status' => 'cancelled']);

        $this->assertDatabaseHas('pc_builds', ['id' => $build->id, 'status' => 'cancelled']);
        $this->assertSame(1, $product->fresh()->stock_quantity);
    }
}
