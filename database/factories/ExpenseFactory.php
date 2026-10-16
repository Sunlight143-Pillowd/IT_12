<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recorded_by' => User::factory(),
            'type' => 'Utilities',
            'description' => fake()->sentence(),
            'amount' => 1000,
            'expense_date' => today(),
            'budget_code' => fake()->bothify('BUD-###'),
        ];
    }
}
