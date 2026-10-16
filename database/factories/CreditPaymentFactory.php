<?php

namespace Database\Factories;

use App\Models\CreditPayment;
use App\Models\CustomerCredit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CreditPayment>
 */
class CreditPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_credit_id' => CustomerCredit::factory(),
            'received_by' => User::factory(),
            'amount' => 100,
            'paid_at' => now(),
        ];
    }
}
