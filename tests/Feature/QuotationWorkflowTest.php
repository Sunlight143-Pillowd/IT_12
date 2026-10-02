<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_saved_quotation_can_be_viewed_with_its_purpose_and_items(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['name' => 'Quotation Monitor']);
        $quotation = Quotation::create([
            'employee_id' => $user->id,
            'customer_name' => 'Quotation Customer',
            'customer_contact' => '09170000000',
            'notes' => 'Explain the workstation configuration and price.',
            'total_amount' => 5000,
        ]);
        $quotation->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Quotation Monitor',
            'unit_price' => 2500,
            'quantity' => 2,
            'subtotal' => 5000,
        ]);

        $index = $this->actingAs($user)->get(route('quotation.index'));
        $index->assertOk();
        $index->assertSee(route('quotation.show', $quotation), false);
        $index->assertSee('>View</a>', false);

        $detail = $this->actingAs($user)->get(route('quotation.show', $quotation));
        $detail->assertOk();
        $detail->assertSee('Quotation Customer');
        $detail->assertSee('Explain the workstation configuration and price.');
        $detail->assertSee('Quotation Monitor');
        $detail->assertSee('₱5,000.00');
    }
}
