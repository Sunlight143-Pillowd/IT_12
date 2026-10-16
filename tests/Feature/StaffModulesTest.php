<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_records_sale_payment_and_reduces_inventory(): void
    {
        $cashierRole = Role::query()->where('slug', 'cashier')->firstOrFail();
        $cashier = User::factory()->create(['role_id' => $cashierRole->id]);
        $product = Product::factory()->create([
            'price' => 1250,
            'stock_quantity' => 4,
        ]);
        $this->assertTrue($cashier->hasPermission('sales.create'));

        $response = $this->actingAs($cashier)->post(route('pos.checkout'), [
            'cart_json' => json_encode([
                ['product_id' => $product->id, 'quantity' => 2],
            ], JSON_THROW_ON_ERROR),
            'payment_method' => 'cash',
            'payment_amount' => 2500,
        ]);

        $response->assertRedirect();
        $sale = Sale::query()->sole();
        $response->assertRedirect(route('pos.receipt', $sale));
        $this->assertSame('2500.00', $sale->total_amount);
        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 2500,
        ]);
        $this->assertDatabaseHas('sale_payments', [
            'sale_id' => $sale->id,
            'method' => 'cash',
            'amount' => 2500,
            'amount_tendered' => 2500,
        ]);
        $this->assertSame(2, $product->fresh()->stock_quantity);
    }

    public function test_cashier_cannot_open_inventory_management(): void
    {
        $cashierRole = Role::query()->where('slug', 'cashier')->firstOrFail();
        $cashier = User::factory()->create(['role_id' => $cashierRole->id]);

        $this->actingAs($cashier)
            ->get(route('inventory.index'))
            ->assertForbidden();
    }

    public function test_credit_payment_reduces_balance_and_rejects_overpayment(): void
    {
        $cashierRole = Role::query()->where('slug', 'cashier')->firstOrFail();
        $cashier = User::factory()->create(['role_id' => $cashierRole->id]);
        $this->assertTrue($cashier->hasPermission('credit.manage'));
        $customer = Customer::factory()->create();
        $credit = CustomerCredit::factory()->for($customer)->create([
            'amount_due' => 500,
            'balance_due' => 500,
        ]);

        $this->actingAs($cashier)
            ->post(route('credits.payments.store', $credit), ['amount' => 200])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('300.00', $credit->fresh()->balance_due);
        $this->assertDatabaseHas('credit_payments', [
            'customer_credit_id' => $credit->id,
            'received_by' => $cashier->id,
            'amount' => 200,
        ]);

        $this->from(route('customers.index'))
            ->post(route('credits.payments.store', $credit), ['amount' => 301])
            ->assertRedirect(route('customers.index'))
            ->assertSessionHasErrors([
                'amount' => 'Payment cannot exceed the outstanding credit balance.',
            ]);

        $this->assertSame('300.00', $credit->fresh()->balance_due);
        $this->assertDatabaseCount('credit_payments', 1);
    }

    public function test_staff_roles_can_open_their_assigned_back_office_modules(): void
    {
        $cashier = $this->userWithRole('cashier');
        $inventoryStaff = $this->userWithRole('inventory');
        $technicalStaff = $this->userWithRole('technical');
        $owner = $this->userWithRole('owner');

        $this->actingAs($cashier)->get(route('customers.index'))->assertOk();
        $this->actingAs($technicalStaff)->get(route('pos.index'))->assertForbidden();
        $this->actingAs($inventoryStaff)->get(route('suppliers.index'))->assertOk();
        $this->actingAs($technicalStaff)->get(route('warranty.index'))->assertOk();
        $this->actingAs($owner)->get(route('employees.index'))->assertOk();
        $this->actingAs($owner)->get(route('roles.index'))->assertOk();
        $this->actingAs($owner)->get(route('expenses.index'))->assertOk();
        $this->actingAs($owner)->get(route('reports.index'))->assertOk();
    }

    public function test_owner_can_record_expenses_and_view_them_in_reports(): void
    {
        $owner = $this->userWithRole('owner');
        $customer = Customer::factory()->create();
        CustomerCredit::factory()->for($customer)->create([
            'credit_date' => today(),
            'amount_due' => 50,
            'balance_due' => 50,
        ]);

        $this->actingAs($owner)
            ->post(route('expenses.store'), [
                'type' => 'Utilities',
                'description' => 'Monthly internet bill',
                'amount' => 1800,
                'expense_date' => today()->toDateString(),
                'budget_code' => 'OPS-01',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $expense = Expense::query()->sole();
        $this->assertSame('1800.00', $expense->amount);
        $this->assertSame($owner->id, $expense->recorded_by);
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'description' => 'Monthly internet bill',
            'budget_code' => 'OPS-01',
        ]);
        $this->actingAs($owner)
            ->get(route('reports.index', [
                'start' => today()->toDateString(),
                'end' => today()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Utilities')
            ->assertSee('1,800.00')
            ->assertSee('₱50.00');
    }

    private function userWithRole(string $slug): User
    {
        $role = Role::query()->where('slug', $slug)->firstOrFail();

        return User::factory()->create(['role_id' => $role->id]);
    }
}
