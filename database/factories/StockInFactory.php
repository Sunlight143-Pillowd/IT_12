<?php

namespace Database\Factories;

use App\Models\StockIn;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockIn>
 */
class StockInFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => User::factory(),
            'supplier_name' => fake()->company(),
            'supplier_contact' => fake()->phoneNumber(),
            'invoice_number' => fake()->unique()->bothify('INV-#####'),
            'received_at' => now(),
        ];
    }
}
