<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    public static function productTypeOptions(): array
    {
        return [
            'desktop' => 'Desktop',
            'laptop' => 'Laptop',
            'accessory' => 'Accessory',
            'gpu' => 'GPU',
            'cpu' => 'CPU',
            'monitor' => 'Monitor',
            'mouse' => 'Mouse',
            'keyboard' => 'Keyboard',
            'case' => 'Case',
            'fan' => 'Fan',
            'cpu_cooler' => 'CPU Cooler',
            'ssd' => 'SSD',
            'ram' => 'RAM',
            'motherboard' => 'Motherboard',
            'power_supply' => 'Power Supply',
            'speaker' => 'Speaker',
            'headset' => 'Headset',
            'printer' => 'Printer',
            'router' => 'Router',
            'storage' => 'Storage',
            'networking' => 'Networking',
        ];
    }

    public function index(Request $request): View
    {
        $selectedCategory = trim((string) $request->query('category', ''));

        $products = Product::query()
            ->when($selectedCategory !== '' && $selectedCategory !== 'all', function ($query) use ($selectedCategory) {
                $query->where('category', $selectedCategory);
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $categoryRecords = Category::orderBy('name')->get();
        $categories = $categoryRecords->pluck('name')->toArray();
        $fallbackCategories = ['GPU', 'CPU', 'Monitor', 'Mouse', 'Keyboard', 'Case', 'Fans', 'CPU Cooler', 'SSD', 'RAM'];
        $categories = array_values(array_unique(array_merge($categories, $fallbackCategories)));

        return view('inventory', [
            'products' => $products,
            'categories' => $categories,
            'categoryRecords' => $categoryRecords,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', Rule::in(array_keys(self::productTypeOptions()))],
            'category' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'stock_location' => ['required', 'string'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Product::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . time(),
            'type' => $data['type'],
            'category' => $data['category'],
            'price' => (int) $data['price'],
            'stock_quantity' => (int) $data['stock_quantity'],
            'stock_location' => $data['stock_location'],
            'low_stock_threshold' => (int) ($data['low_stock_threshold'] ?? 5),
            'description' => $data['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Product added to inventory.');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'stock_location' => ['required', 'string'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $product->update([
            'stock_quantity' => $data['stock_quantity'],
            'stock_location' => $data['stock_location'],
        ]);

        return redirect()->route('inventory.index')->with('success', 'Inventory updated.');
    }
}
