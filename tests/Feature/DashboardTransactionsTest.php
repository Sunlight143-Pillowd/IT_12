<?php

namespace Tests\Feature;

use App\Models\PcBuild;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockIn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTransactionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_lists_sales_and_delivery_invoice_links_to_details(): void
    {
        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
        $customer = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Dashboard Receipt CPU',
            'description' => 'AM5 desktop processor, 6-core model.',
        ]);
        $sale = Sale::create([
            'employee_id' => $admin->id,
            'customer_name' => 'Dashboard Customer',
            'total_amount' => 7200,
        ]);
        $sale->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Dashboard Receipt CPU',
            'unit_price' => 7200,
            'quantity' => 1,
            'subtotal' => 7200,
        ]);
        $stockIn = StockIn::factory()->create([
            'employee_id' => $admin->id,
            'supplier_name' => 'Dashboard Supplier',
            'invoice_number' => 'SUP-INV-500',
        ]);

        $dashboard = $this->actingAs($admin)->get(route('dashboard'));
        $dashboard->assertOk();
        $dashboard->assertSee('Recent Sales');
        $dashboard->assertSee('Stock-In Transactions');
        $dashboard->assertSee('Dashboard Customer');
        $dashboard->assertSee('Dashboard Receipt CPU');
        $dashboard->assertSee('Dashboard Supplier');
        $dashboard->assertSee('SUP-INV-500');
        $dashboard->assertSee(route('pos.receipt', $sale), false);
        $dashboard->assertSee(route('stock-in.index'), false);

        $pos = $this->actingAs($admin)->get(route('pos.index'));
        $pos->assertOk();
        $pos->assertSee('id="history-search"', false);
        $pos->assertSee('Search receipts, builds, invoices, suppliers, products...');
        $pos->assertSee('id="history-no-results"', false);
        $pos->assertSee('detailTemplate?.content.textContent', false);

        $receipt = $this->actingAs($customer)->get(route('pos.receipt', $sale));
        $receipt->assertOk();
        $receipt->assertSee('Dashboard Customer');
        $receipt->assertSee('Dashboard Receipt CPU');
        $receipt->assertSee('AM5 desktop processor, 6-core model.');
        $receipt->assertSee('₱7,200.00');
    }

    public function test_pos_lists_supplier_invoices_and_charts_sales_and_accepted_customer_builds(): void
    {
        $employee = User::factory()->create();
        $customer = User::factory()->create();
        $product = Product::factory()->create(['name' => 'Analytics CPU']);
        $sale = Sale::create([
            'employee_id' => $employee->id,
            'customer_name' => 'Analytics Sale Customer',
            'total_amount' => 7000,
        ]);
        $sale->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 7000,
            'quantity' => 1,
            'subtotal' => 7000,
        ]);
        $build = PcBuild::create([
            'build_number' => 'PC-ANALYTICS-0001',
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'user_id' => $customer->id,
            'status' => 'accepted',
            'total_cost' => 4500,
            'stock_deducted_at' => now(),
        ]);
        $build->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 4500,
            'subtotal' => 4500,
        ]);
        $stockIn = StockIn::factory()->create([
            'supplier_name' => 'Analytics Supplier',
            'invoice_number' => 'ANALYTICS-INV-1',
            'received_at' => now(),
        ]);
        $stockIn->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_cost' => 800,
        ]);

        $this->actingAs($employee)
            ->get(route('pos.index'))
            ->assertOk()
            ->assertSee('Supplier Invoices (1)')
            ->assertSee('ANALYTICS-INV-1')
            ->assertSee('Analytics Supplier')
            ->assertSee('Analytics CPU')
            ->assertSee('Customer PC Build Receipt')
            ->assertSee('₱11,500.00')
            ->assertSee('₱1,600.00')
            ->assertSee('id="pos-analytics"', false)
            ->assertSee('id="pos-line-chart"', false)
            ->assertSee('Receipts + accepted customer PC builds');
    }
}
