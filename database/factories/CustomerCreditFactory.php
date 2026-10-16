<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerCredit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerCredit>
 */
class CustomerCreditFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'credit_date' => today(),
            'description' => fake()->sentence(),
            'amount_due' => 1000,
            'balance_due' => 1000,
        ];
    }
}
