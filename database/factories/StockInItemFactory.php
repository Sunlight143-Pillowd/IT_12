<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockInItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockInItem>
 */
class StockInItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock_in_id' => StockIn::factory(),
            'product_id' => Product::factory(),
            'quantity' => 1,
            'unit_cost' => fake()->numberBetween(100, 50000),
        ];
    }
}
