<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

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
        $search = trim((string) $request->query('search', ''));
        $stockSort = $request->query('stock_sort', 'name');
        $stockSort = in_array($stockSort, ['name', 'ascending', 'descending'], true)
            ? $stockSort
            : 'name';

        $products = Product::query()
            ->with('stockInItems.stockIn')
            ->when($selectedCategory !== '' && $selectedCategory !== 'all', function ($query) use ($selectedCategory) {
                $query->where('category', $selectedCategory);
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('sku', 'like', '%'.$search.'%')
                        ->orWhere('type', 'like', '%'.$search.'%')
                        ->orWhere('category', 'like', '%'.$search.'%');
                });
            })
            ->orderByRaw('CASE WHEN stock_quantity <= low_stock_threshold THEN 0 ELSE 1 END')
            ->when($stockSort === 'ascending', fn ($query) => $query->orderBy('stock_quantity')->orderBy('name'))
            ->when($stockSort === 'descending', fn ($query) => $query->orderByDesc('stock_quantity')->orderBy('name'))
            ->when($stockSort === 'name', fn ($query) => $query->orderBy('type')->orderBy('name'))
            ->get();

        $categoryRecords = Category::query()->orderBy('sort_order')->orderBy('id')->get();
        $categories = $categoryRecords->pluck('name')->all();

        return view('inventory', [
            'products' => $products,
            'categories' => $categories,
            'categoryRecords' => $categoryRecords,
            'selectedCategory' => $selectedCategory,
            'search' => $search,
            'stockSort' => $stockSort,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'type' => ['required', 'string', Rule::in(array_keys(self::productTypeOptions()))],
            'category' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_location' => ['required', 'string'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
            'requires_serial' => ['nullable', 'boolean'],
            'warranty_months' => ['nullable', 'integer', 'min:0', 'max:1200'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'before_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'after_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $imagePaths = [];
        foreach ([
            'image' => 'image_path',
            'before_image' => 'before_image_path',
            'after_image' => 'after_image_path',
        ] as $input => $column) {
            if ($request->hasFile($input)) {
                $imagePaths[$column] = $request->file($input)->store('products', 'public');
            }
        }

        $product = Product::create([
            'name' => $data['name'],
            'sku' => $data['sku'] ?? null,
            'slug' => Str::slug($data['name']).'-'.time(),
            'type' => $data['type'],
            'category' => $data['category'],
            'price' => (int) $data['price'],
            'stock_quantity' => 0,
            'stock_location' => $data['stock_location'],
            'low_stock_threshold' => (int) ($data['low_stock_threshold'] ?? 5),
            'description' => $data['description'] ?? null,
            'requires_serial' => $request->boolean('requires_serial'),
            'warranty_months' => $data['warranty_months'] ?? null,
            ...$imagePaths,
            'is_active' => true,
        ]);
        if (blank($product->sku)) {
            $product->update(['sku' => 'CB-'.str_pad((string) $product->id, 6, '0', STR_PAD_LEFT)]);
        }

        return redirect()->route('inventory.index')->with('success', 'Product added to inventory.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->pluck('name')->all();

        return view('inventory-edit', [
            'product' => $product,
            'categories' => $categories,
            'productTypeOptions' => self::productTypeOptions(),
        ]);
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product)],
            'type' => ['required', 'string', Rule::in(array_keys(self::productTypeOptions()))],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_location' => ['required', 'string', Rule::in(['warehouse', 'store', 'used_in_pc'])],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
            'requires_serial' => ['nullable', 'boolean'],
            'warranty_months' => ['nullable', 'integer', 'min:0', 'max:1200'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'before_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'after_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $product->fill([
            'name' => $data['name'],
            'sku' => $data['sku'] ?: $product->sku,
            'slug' => Str::slug($data['name']).'-'.$product->id,
            'type' => $data['type'],
            'category' => $data['category'],
            'price' => (int) $data['price'],
            'stock_location' => $data['stock_location'],
            'low_stock_threshold' => $data['low_stock_threshold'],
            'description' => $data['description'] ?? null,
            'requires_serial' => $request->boolean('requires_serial'),
            'warranty_months' => $data['warranty_months'] ?? null,
        ]);

        foreach ([
            'image' => 'image_path',
            'before_image' => 'before_image_path',
            'after_image' => 'after_image_path',
        ] as $input => $column) {
            if ($request->hasFile($input)) {
                if ($product->{$column}) {
                    Storage::disk('public')->delete($product->{$column});
                }

                $product->{$column} = $request->file($input)->store('products', 'public');
            }
        }

        $product->save();

        return redirect()->route('inventory.index')->with('success', 'Product details updated.');
    }

    public function uploadFeaturedImage(Request $request, Product $product): RedirectResponse
    {
        abort_unless($request->user()?->hasPermission('inventory.manage'), 403);

        $request->validate([
            'image' => [
                'required',
                'file',
                'max:5120',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $imageInfo = $value instanceof UploadedFile && $value->isValid()
                        ? getimagesize($value->getRealPath())
                        : false;

                    if (! is_array($imageInfo) || ! in_array($imageInfo['mime'] ?? '', [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/bmp',
                        'image/webp',
                    ], true)) {
                        $fail('Please choose a valid JPG, PNG, GIF, BMP, or WEBP image.');
                    }
                },
            ],
        ], [
            'image.max' => 'The image must be 5 MB or smaller.',
        ]);

        $previousImage = $product->image_path;
        $product->update([
            'image_path' => $request->file('image')->store('products', 'public'),
        ]);

        if ($previousImage) {
            Storage::disk('public')->delete($previousImage);
        }

        return back()->with('status', 'Photo uploaded for '.$product->name.'.');
    }
}
