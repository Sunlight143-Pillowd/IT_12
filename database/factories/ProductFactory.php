<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'type' => 'accessory',
            'category' => 'General',
            'price' => fake()->numberBetween(100, 100000),
            'stock_quantity' => 0,
            'low_stock_threshold' => 5,
            'stock_location' => 'warehouse',
            'requires_serial' => false,
            'is_active' => true,
        ];
    }
}
