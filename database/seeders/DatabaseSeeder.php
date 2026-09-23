<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@davaobosscomputer.com'],
            [
                'name' => 'Site Admin',
                'password' => bcrypt('admin123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        \App\Models\Product::query()->delete();

        $products = [
            ['name' => 'Boss Strike ITX', 'type' => 'desktop', 'category' => 'Ready to Ship', 'price' => 54999, 'stock_quantity' => 14, 'low_stock_threshold' => 5, 'stock_location' => 'warehouse'],
            ['name' => 'Boss Vanguard Mid', 'type' => 'desktop', 'category' => 'Ready to Ship', 'price' => 74999, 'stock_quantity' => 11, 'low_stock_threshold' => 5, 'stock_location' => 'warehouse'],
            ['name' => 'Boss Apex 4K', 'type' => 'desktop', 'category' => 'Gaming', 'price' => 119999, 'stock_quantity' => 7, 'low_stock_threshold' => 5, 'stock_location' => 'store'],
            ['name' => 'Boss Nomad 15', 'type' => 'laptop', 'category' => 'Thin & Light', 'price' => 69999, 'stock_quantity' => 8, 'low_stock_threshold' => 5, 'stock_location' => 'warehouse'],
            ['name' => 'Boss Overclock 16', 'type' => 'laptop', 'category' => 'Performance', 'price' => 94999, 'stock_quantity' => 6, 'low_stock_threshold' => 5, 'stock_location' => 'store'],
            ['name' => 'Boss Mechanical Keyboard', 'type' => 'accessory', 'category' => 'Peripherals', 'price' => 3499, 'stock_quantity' => 18, 'low_stock_threshold' => 5, 'stock_location' => 'store'],
            ['name' => 'Boss Wireless Mouse', 'type' => 'accessory', 'category' => 'Peripherals', 'price' => 2299, 'stock_quantity' => 27, 'low_stock_threshold' => 5, 'stock_location' => 'warehouse'],
            ['name' => '27\" 1440p Monitor', 'type' => 'accessory', 'category' => 'Displays', 'price' => 12999, 'stock_quantity' => 4, 'low_stock_threshold' => 5, 'stock_location' => 'store'],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create([
                'name' => $product['name'],
                'slug' => \Illuminate\Support\Str::slug($product['name']) . '-' . rand(100, 999),
                'type' => $product['type'],
                'category' => $product['category'],
                'price' => $product['price'],
                'stock_quantity' => $product['stock_quantity'],
                'low_stock_threshold' => $product['low_stock_threshold'],
                'stock_location' => $product['stock_location'],
                'description' => 'System inventory item',
                'is_active' => true,
            ]);
        }
    }
}
