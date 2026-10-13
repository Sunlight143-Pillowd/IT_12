<?php

namespace Tests\Feature;

use App\Models\PcBuild;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\StockIn;
use App\Models\User;
use App\Services\PcBuildService;
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
            ->assertSee('36 months')
            ->assertSee('VAT (12%, included)')
            ->assertSee('₱1,285.71');
    }

    public function test_reserved_build_can_be_edited_and_sold_once_with_component_and_bundle_stock_decremented(): void
    {
        $employee = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $product = Product::factory()->create([
            'type' => 'cpu',
            'category' => 'CPU',
            'price' => 12000,
            'stock_quantity' => 1,
        ]);

        $this->actingAs($employee)->post(route('buildpc.store'), [
            'customer_name' => 'Build Sale Customer',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertRedirect(route('buildpc.index'));

        $build = PcBuild::firstOrFail();
        $this->assertNull($build->user_id);
        $this->assertDatabaseHas('stock_reservations', [
            'pc_build_id' => $build->id,
            'product_id' => $product->id,
            'status' => 'active',
        ]);
        $this->assertSame(1, app(PcBuildService::class)->availableQuantity($product, $build->id));

        $this->actingAs($employee)
            ->get(route('buildpc.edit', $build))
            ->assertOk()
            ->assertSee('(Avail: 1)');

        $this->actingAs($employee)
            ->patch(route('buildpc.update', $build), [
                'items' => ['cpu' => ['product_id' => $product->id]],
            ])
            ->assertRedirect(route('buildpc.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, $product->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_reservations', [
            'pc_build_id' => $build->id,
            'product_id' => $product->id,
            'status' => 'active',
        ]);

        $this->actingAs($employee)->post(route('buildpc.sell', $build))->assertRedirect();

        $bundle = Product::query()->whereKey($build->fresh()->product_id)->firstOrFail();
        $this->assertSame(0, $product->fresh()->stock_quantity);
        $this->assertSame(0, $bundle->fresh()->stock_quantity);
        $this->assertDatabaseHas('pc_builds', ['id' => $build->id, 'status' => 'sold']);
        $this->assertDatabaseCount('sales', 1);

        $this->actingAs($employee)
            ->from(route('buildpc.index'))
            ->post(route('buildpc.sell', $build))
            ->assertRedirect(route('buildpc.index'))
            ->assertSessionHasErrors('status');

        $this->assertSame(0, $product->fresh()->stock_quantity);
        $this->assertDatabaseCount('sales', 1);
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
            ->assertSee('href="'.route('dashboard').'"', false)
            ->assertSee('border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-600 hover:text-purple-600', false)
            ->assertSee('Customer Build / PC Build Management')
            ->assertSee('No customer PC builds have been submitted.')
            ->assertDontSee('Other Components')
            ->assertSee('id="build-summary-body"', false)
            ->assertSee("document.querySelectorAll('.build-photo-input').forEach", false)
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
            ->assertSee('name="components[cpu]"', false)
            ->assertSee('name="components[ram]"', false)
            ->assertSee('Build CPU')
            ->assertSee('Build RAM')
            ->assertSee('Choose your components')
            ->assertSee('Estimated receipt')
            ->assertSee('VAT (12%, included)')
            ->assertSee('Submit build request')
            ->assertDontSee('type="text" name="components[cpu]"', false)
            ->assertDontSee('Other Components')
            ->assertDontSee('name="items[cpu][quantity]"')
            ->assertDontSee('>Qty<', false);

        $this->actingAs($customer)->post(route('buildpc.customer.store'), [
            'components' => [
                'cpu' => (string) $cpu->id,
                'ram' => (string) $ram->id,
            ],
            'notes' => 'Quiet workstation, budget-conscious.',
            'estimated_cost' => 0.01,
        ])->assertRedirect(route('buildpc.customer'));

        $build = PcBuild::query()->where('user_id', $customer->id)->firstOrFail();
        $this->assertDatabaseHas('pc_builds', [
            'id' => $build->id,
            'customer_name' => 'Build Customer',
            'status' => 'pending',
            'total_cost' => 15000,
            'estimated_cost' => 15000,
            'notes' => "CPU: Build CPU\nRAM: Build RAM\nAdditional notes: Quiet workstation, budget-conscious.",
        ]);
        $this->assertSame(15000.0, $build->estimated_cost);
        $this->assertDatabaseCount('pc_build_items', 0);
        $this->assertSame(2, $cpu->fresh()->stock_quantity);
        $this->assertSame(4, $ram->fresh()->stock_quantity);

        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertDontSee('Customer Build / PC Build Management');

        $this->actingAs($admin)
            ->get(route('buildpc.index'))
            ->assertSee('Customer Build / PC Build Management')
            ->assertDontSee('Other Components')
            ->assertSee($build->build_number)
            ->assertSee('CPU: Build CPU')
            ->assertSee('RAM: Build RAM')
            ->assertSee('accept=".jpg,.jpeg,.png"', false)
            ->assertSee('class="space-y-3 p-4"', false)
            ->assertSee('Estimated price (VAT included): ₱15,000.00')
            ->assertSee('₱1,607.14');

        $this->actingAs($customer)
            ->get(route('buildpc.customer'))
            ->assertSee('Estimated price (VAT included): ₱15,000.00')
            ->assertSee('VAT (12%, included)')
            ->assertSee('₱1,607.14');

        $this->actingAs($customer)
            ->get(route('buildpc.show', $build))
            ->assertSee('Estimated price: ₱15,000.00')
            ->assertSee('VAT (12%, included)')
            ->assertSee('₱1,607.14');

        $this->actingAs($customer)
            ->get(route('buildpc.print', $build))
            ->assertSee('Estimated price: ₱15,000.00')
            ->assertSee('VAT (12%, included): ₱1,607.14');

        $this->actingAs($admin)
            ->from(route('buildpc.index'))
            ->patch(route('dashboard.customer-builds.update', $build), [
                'status' => 'accepted',
                'product_photo' => UploadedFile::fake()->create('build-product.png', 10, 'image/png'),
                'before_photo' => UploadedFile::fake()->create('build-before.png', 10, 'image/png'),
                'after_photo' => UploadedFile::fake()->create('build-after.jpg', 10, 'image/jpeg'),
            ])
            ->assertRedirect(route('buildpc.index'));

        $build->refresh();
        $this->assertSame('accepted', $build->status);
        $this->assertNull($build->stock_deducted_at);
        $this->assertNotNull($build->product_photo_path);
        $this->assertNotNull($build->before_photo_path);
        $this->assertNotNull($build->after_photo_path);
        Storage::disk('public')->assertExists($build->product_photo_path);
        Storage::disk('public')->assertExists($build->before_photo_path);
        Storage::disk('public')->assertExists($build->after_photo_path);
        $this->assertSame(2, $cpu->fresh()->stock_quantity);
        $this->assertSame(4, $ram->fresh()->stock_quantity);

        $this->actingAs($admin)
            ->patch(route('dashboard.customer-builds.update', $build), ['notes' => 'CPU: Admin-edited specification'])
            ->assertRedirect(route('buildpc.index'))
            ->assertSessionHasNoErrors();
        $this->assertSame('accepted', $build->fresh()->status);
        $this->assertSame('CPU: Admin-edited specification', $build->fresh()->notes);

        $this->actingAs($customer)
            ->get(route('dashboard'))
            ->assertSee('My PC Builds')
            ->assertSee($build->build_number)
            ->assertSee('Accepted')
            ->assertSee('Estimated price: ₱15,000.00')
            ->assertSee('Admin-edited specification')
            ->assertSee('Product photo');

        foreach (['building', 'testing', 'ready', 'completed'] as $status) {
            $this->actingAs($admin)
                ->patch(route('dashboard.customer-builds.update', $build), ['status' => $status])
                ->assertRedirect();
        }

        $this->assertDatabaseHas('pc_builds', ['id' => $build->id, 'status' => 'completed']);
    }

    public function test_guest_can_view_build_pc_but_is_redirected_to_login_when_submitting(): void
    {
        $customer = User::factory()->create();
        $cpu = Product::factory()->create([
            'name' => 'Saved guest CPU',
            'type' => 'cpu',
            'category' => 'CPU',
            'stock_quantity' => 2,
        ]);

        $this->get(route('buildpc.customer'))
            ->assertOk()
            ->assertSee('Build PC')
            ->assertSee('Submit build request')
            ->assertSee('You’ll be asked to sign in before submitting.');

        $this->post(route('buildpc.customer.store'), [
            'customer_name' => 'Guest Build Customer',
            'customer_email' => 'guest@example.com',
            'components' => ['cpu' => (string) $cpu->id],
            'notes' => 'Keep this draft after signing in.',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('url.intended', route('buildpc.customer'));

        $this->post(route('login'), [
            'email' => $customer->email,
            'password' => 'password',
        ])->assertRedirect(route('buildpc.customer'));

        $response = $this->get(route('buildpc.customer'));
        $response
            ->assertOk()
            ->assertSee('Build your PC')
            ->assertSee('value="Guest Build Customer"', false)
            ->assertSee('value="guest@example.com"', false)
            ->assertSee('Keep this draft after signing in.');
        $this->assertMatchesRegularExpression('/<option[^>]*value="'.$cpu->id.'"[^>]*selected/', $response->getContent());

        $this->assertDatabaseCount('pc_builds', 0);
    }

    public function test_customer_cannot_submit_an_empty_build_request(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->from(route('buildpc.customer'))
            ->post(route('buildpc.customer.store'), [
                'customer_name' => $customer->name,
                'components' => ['cpu' => '', 'ram' => ''],
            ])
            ->assertRedirect(route('buildpc.customer'))
            ->assertSessionHasErrors('components');

        $this->assertDatabaseCount('pc_builds', 0);
    }

    public function test_customer_cannot_select_a_product_from_the_wrong_component_group(): void
    {
        $customer = User::factory()->create();
        $ram = Product::factory()->create([
            'name' => 'Wrong slot RAM',
            'type' => 'ram',
            'category' => 'RAM',
            'stock_quantity' => 2,
        ]);

        $this->actingAs($customer)
            ->from(route('buildpc.customer'))
            ->post(route('buildpc.customer.store'), [
                'components' => ['cpu' => $ram->id],
            ])
            ->assertRedirect(route('buildpc.customer'))
            ->assertSessionHasErrors('components.cpu');

        $this->assertDatabaseCount('pc_builds', 0);
    }

    public function test_customer_build_photo_upload_rejects_non_jpeg_png_extensions(): void
    {
        Storage::fake('public');
        $customer = User::factory()->create();
        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $build = PcBuild::create([
            'build_number' => 'PC-PHOTO-0001',
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'user_id' => $customer->id,
            'status' => 'pending',
            'total_cost' => 0,
        ]);

        $this->actingAs($admin)
            ->from(route('buildpc.index'))
            ->patch(route('dashboard.customer-builds.update', $build), [
                'product_photo' => UploadedFile::fake()->createWithContent(
                    'build-product.webp',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/WocAAAAASUVORK5CYII=')
                ),
            ])
            ->assertRedirect(route('buildpc.index'))
            ->assertSessionHasErrors('product_photo');

        $this->assertNull($build->fresh()->product_photo_path);
        $this->assertSame([], Storage::disk('public')->allFiles());
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

    public function test_deleting_an_accepted_customer_build_restores_its_committed_stock(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create([
            'type' => 'cpu',
            'category' => 'CPU',
            'stock_quantity' => 1,
        ]);
        $build = PcBuild::create([
            'build_number' => 'PC-DELETE-0001',
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

        $this->actingAs($admin)
            ->patch(route('dashboard.customer-builds.update', $build), ['status' => 'accepted'])
            ->assertRedirect();
        $this->assertSame(0, $product->fresh()->stock_quantity);

        $this->actingAs($customer)
            ->delete(route('buildpc.customer.destroy', $build))
            ->assertRedirect();

        $this->assertDatabaseMissing('pc_builds', ['id' => $build->id]);
        $this->assertSame(1, $product->fresh()->stock_quantity);
    }
}
