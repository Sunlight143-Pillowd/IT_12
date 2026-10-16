<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\ServiceCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceCase>
 */
class ServiceCaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'case_number' => fake()->unique()->bothify('CASE-######'),
            'customer_id' => Customer::factory(),
            'created_by' => User::factory(),
            'case_type' => 'warranty',
            'status' => 'open',
            'description' => fake()->sentence(),
        ];
    }
}
