<?php

namespace Tests\Feature;

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
<<<<<<< HEAD
        $admin = User::factory()->create(['email' => 'admin@davaobosscomputer.com']);
=======
        $admin = User::factory()->create(['email' => 'admin@computerbossdavao.com']);
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
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
<<<<<<< HEAD
=======
        $dashboard->assertSee('Dashboard Receipt CPU');
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
        $dashboard->assertSee('Dashboard Supplier');
        $dashboard->assertSee('SUP-INV-500');
        $dashboard->assertSee(route('pos.receipt', $sale), false);
        $dashboard->assertSee(route('stock-in.index'), false);

        $pos = $this->actingAs($admin)->get(route('pos.index'));
        $pos->assertOk();
        $pos->assertSee('id="history-search"', false);
        $pos->assertSee('Search receipts, customers, items, serial numbers...');
        $pos->assertSee('id="history-no-results"', false);
        $pos->assertSee('detailTemplate?.content.textContent', false);

        $receipt = $this->actingAs($customer)->get(route('pos.receipt', $sale));
        $receipt->assertOk();
        $receipt->assertSee('Dashboard Customer');
        $receipt->assertSee('Dashboard Receipt CPU');
        $receipt->assertSee('AM5 desktop processor, 6-core model.');
        $receipt->assertSee('₱7,200.00');
    }
}
