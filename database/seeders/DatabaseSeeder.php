<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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

        $products = [
            ['name' => 'Boss Strike ITX', 'type' => 'desktop', 'category' => 'Ready to Ship', 'price' => 54999, 'stock_quantity' => 14, 'low_stock_threshold' => 5, 'stock_location' => 'warehouse', 'image_path' => 'products/3N2lr3q3LeDrWS8NQoGsPHmtcMC84Ji69Gk3q0LH.jpg'],
            ['name' => 'Boss Vanguard Mid', 'type' => 'desktop', 'category' => 'Ready to Ship', 'price' => 74999, 'stock_quantity' => 11, 'low_stock_threshold' => 5, 'stock_location' => 'warehouse', 'image_path' => 'categories/1jTumgBf3ozP25LWEDSI8meNgdhwCLnddeSknYNq.jpg'],
            ['name' => 'Boss Apex 4K', 'type' => 'desktop', 'category' => 'Gaming', 'price' => 119999, 'stock_quantity' => 7, 'low_stock_threshold' => 5, 'stock_location' => 'store', 'image_path' => 'products/CxNOoMnARYkrQiwRf9wploW5lXIUULzImvPl7Kwl.jpg'],
            ['name' => 'Boss Nomad 15', 'type' => 'laptop', 'category' => 'Thin & Light', 'price' => 69999, 'stock_quantity' => 8, 'low_stock_threshold' => 5, 'stock_location' => 'warehouse', 'image_path' => 'products/NDTThhwZdO1YBxrgiFyND1l5s0jXp2e46itvy2rS.jpg'],
            ['name' => 'Boss Overclock 16', 'type' => 'laptop', 'category' => 'Performance', 'price' => 94999, 'stock_quantity' => 6, 'low_stock_threshold' => 5, 'stock_location' => 'store', 'image_path' => 'products/kAYE23KfKnWOmdzzau3pcggfvcJnGeIOUTjRqSC4.jpg'],
            ['name' => 'Boss Mechanical Keyboard', 'type' => 'accessory', 'category' => 'Peripherals', 'price' => 3499, 'stock_quantity' => 18, 'low_stock_threshold' => 5, 'stock_location' => 'store', 'image_path' => 'products/vgolPDjGOgGZ04u84typwgUblOjLpu6OzbznFuqf.jpg'],
            ['name' => 'Boss Wireless Mouse', 'type' => 'accessory', 'category' => 'Peripherals', 'price' => 2299, 'stock_quantity' => 27, 'low_stock_threshold' => 5, 'stock_location' => 'warehouse', 'image_path' => 'products/GukOLwKANPFr38OfF8QMj11Pv62LUKK49ochNKW1.jpg'],
            ['name' => '27\" 1440p Monitor', 'type' => 'accessory', 'category' => 'Displays', 'price' => 12999, 'stock_quantity' => 4, 'low_stock_threshold' => 5, 'stock_location' => 'store', 'image_path' => 'products/RcaZjdmeTQrLV53IIIFKbSliPK2l2tqLl1G6Nlsc.jpg'],
        ];

        foreach ($products as $product) {
            Category::firstOrCreate(
                ['slug' => Str::slug($product['category'])],
                ['name' => $product['category']]
            );

            $slug = Str::slug($product['name']);
            $productRecord = Product::query()
                ->where('slug', $slug)
                ->orWhere('name', $product['name'])
                ->first();

            if (! $productRecord) {
                $productRecord = Product::create([
                    'name' => $product['name'],
                    'slug' => $slug,
                    'type' => $product['type'],
                    'category' => $product['category'],
                    'price' => $product['price'],
                    'stock_quantity' => $product['stock_quantity'],
                    'low_stock_threshold' => $product['low_stock_threshold'],
                    'stock_location' => $product['stock_location'],
                    'description' => 'System inventory item',
                    'is_active' => true,
                    'image_path' => $product['image_path'],
                ]);
            }

            if (! $productRecord->image_path) {
                $productRecord->update(['image_path' => $product['image_path']]);
            }
        }
    }
}
