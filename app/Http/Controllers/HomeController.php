<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    private function categoryValueFromFilter(string $filter): string
    {
        return match ($filter) {
            'ready-to-ship' => 'Ready to Ship',
            'gaming' => 'Gaming',
            'workstation' => 'Workstation',
            'thin-and-light' => 'Thin & Light',
            'performance' => 'Performance',
            default => ucfirst(str_replace('-', ' ', $filter)),
        };
    }

    private function categorySlug(string $category): string
    {
        return match (strtolower($category)) {
            'ready to ship' => 'ready-to-ship',
            'gaming' => 'gaming',
            'workstation' => 'workstation',
            'thin & light' => 'thin-and-light',
            'performance' => 'performance',
            default => preg_replace('/\s*&\s*|\s+/', '-', strtolower(trim($category))),
        };
    }

    private function categoryRouteFor(string $category): string
    {
        return route('store.category', ['categorySlug' => Str::slug($category)]);
    }

    private function categoryDisplayName(string $category): string
    {
        $normalized = strtolower(trim($category));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        $displayMap = [
            'custom build' => 'Custom Build',
            'gaming' => 'Gaming',
            'workstation' => 'Workstation',
            'ready to ship' => 'Ready to Ship',
            'ready-to-ship' => 'Ready to Ship',
            'thin & light' => 'Thin & Light',
            'thin-and-light' => 'Thin & Light',
            'performance' => 'Performance',
            'gpu' => 'GPU',
            'psu' => 'Power Supply',
            'graphics card' => 'GPU',
            'graphics cards' => 'GPU',
            'cpu' => 'CPU',
            'case' => 'Case',
            'fans' => 'Fans',
            'cpu cooler' => 'CPU Cooler',
            'ssd' => 'SSD',
            'ram' => 'RAM',
            'memory modules' => 'RAM',
            'power supply' => 'Power Supply',
            'power supplies' => 'Power Supply',
            'motherboard' => 'Motherboard',
            'mother board' => 'Motherboard',
            'motherboards' => 'Motherboard',
            'network attached storage' => 'NAS',
            'network attached storage(nas)' => 'NAS',
            'printers' => 'Printer',
            'routers' => 'Router',
            'processors' => 'Processor',
            'hard drives' => 'Hard Drive',
            'flash drives' => 'Flash Drive',
            'external hard drives' => 'External Drive',
            'external ssd' => 'External SSD',
            'micro sd' => 'Micro SD',
            'monitor' => 'Monitor',
            'monitors' => 'Monitor',
            'display' => 'Monitor',
            'displays' => 'Displays',
            'mouse' => 'Mouse',
            'mice' => 'Mouse',
            'keyboard' => 'Keyboard',
            'keyboards' => 'Keyboard',
            'peripherals' => 'Peripherals',
            'speaker' => 'Speaker',
            'speakers' => 'Speaker',
            'audio' => 'Audio',
            'headset' => 'Headset',
            'headsets' => 'Headset',
        ];

        if (isset($displayMap[$normalized])) {
            return $displayMap[$normalized];
        }

        return ucfirst(str_replace(['-', '_'], ' ', $category));
    }

    public function index(): View
    {
        $categoryRecords = Schema::hasTable('categories')
            ? Category::query()->orderBy('sort_order')->orderBy('id')->get()
            : collect();
        $availableCategories = Schema::hasTable('products')
            ? Product::query()
                ->where('is_active', true)
                ->inStock()
                ->whereNotNull('category')
                ->select('category')
                ->distinct()
                ->pluck('category')
            : collect();
        $categoryCards = $categoryRecords
            ->whereIn('name', $availableCategories)
            ->map(fn (Category $category): array => [
                'label' => $this->categoryDisplayName($category->name),
                'route' => $this->categoryRouteFor($category->name),
                'name' => $category->name,
                'slug' => $category->slug,
                'image_path' => $category->image_path,
            ])
            ->values()
            ->all();

        $categoryImages = $categoryRecords->pluck('image_path', 'slug');
        $specialOffers = Schema::hasTable('products')
            ? Product::query()
                ->where('is_active', true)
                ->inStock()
                ->orderBy('price')
                ->limit(4)
                ->get()
                ->each(function (Product $product) use ($categoryImages): void {
                    $product->setAttribute(
                        'featured_image_path',
                        $product->image_path ?: $categoryImages->get(Str::slug($product->category))
                    );
                })
            : collect();

        $carouselSlides = $specialOffers
            ->map(static fn (Product $product): array => [
                'name' => $product->name,
                'description' => filled($product->description)
                    ? $product->description
                    : 'Special offer: explore '.$product->name.' at Davao Boss Computer.',
            ])
            ->values()
            ->all();

        return view('welcome', compact('categoryCards', 'specialOffers', 'carouselSlides'));
    }

    public function desktops(Request $request): View
    {
        $filter = $request->query('filter', 'all');
        $allowedFilters = ['all', 'ready-to-ship', 'gaming', 'workstation'];
        $filter = in_array($filter, $allowedFilters, true) ? $filter : 'all';

        $products = Schema::hasTable('products')
            ? Product::query()
                ->where('is_active', true)
                ->inStock()
                ->where('type', 'desktop')
                ->when($filter !== 'all', function ($query) use ($filter) {
                    $query->where('category', $this->categoryValueFromFilter($filter));
                })
                ->orderBy('name')
                ->get()
            : collect();

        return view('store.catalog', [
            'title' => 'Gaming Desktops',
            'description' => 'Ready-to-ship and made-to-order towers.',
            'products' => $products,
            'filter' => $filter,
            'type' => 'desktop',
            'filters' => [
                'all' => 'All Desktops',
                'gaming' => 'Gaming',
                'workstation' => 'Workstation',
            ],
        ]);
    }

    public function laptops(Request $request): View
    {
        $filter = $request->query('filter', 'all');
        $allowedFilters = ['all', 'thin-and-light', 'performance'];
        $filter = in_array($filter, $allowedFilters, true) ? $filter : 'all';

        $products = Schema::hasTable('products')
            ? Product::query()
                ->where('is_active', true)
                ->inStock()
                ->where('type', 'laptop')
                ->when($filter !== 'all', function ($query) use ($filter) {
                    $query->where('category', $this->categoryValueFromFilter($filter));
                })
                ->orderBy('name')
                ->get()
            : collect();

        return view('store.catalog', [
            'title' => 'Gaming Laptops',
            'description' => 'Portable builds for gaming on the move.',
            'products' => $products,
            'filter' => $filter,
            'type' => 'laptop',
            'filters' => [
                'all' => 'All Laptops',
                'thin-and-light' => 'Thin & Light',
                'performance' => 'Performance',
            ],
        ]);
    }

    public function accessories(Request $request): View
    {
        $filter = $request->query('filter', 'all');
        $allowedFilters = ['all', 'peripherals', 'displays', 'audio', 'components'];
        $filter = in_array($filter, $allowedFilters, true) ? $filter : 'all';

        $products = Schema::hasTable('products')
            ? Product::query()
                ->where('is_active', true)
                ->inStock()
                ->whereNotIn('type', ['desktop', 'laptop'])
                ->when($filter === 'components', function ($query) {
                    $query->where(function ($query) {
                        $query->whereIn('type', [
                            'gpu',
                            'cpu',
                            'case',
                            'fan',
                            'cpu_cooler',
                            'ssd',
                            'ram',
                            'motherboard',
                            'power_supply',
                            'storage',
                            'networking',
                            'printer',
                            'router',
                        ])->orWhere(function ($query) {
                            $query->where('type', 'accessory')
                                ->whereRaw('LOWER(category) = ?', ['components']);
                        });
                    });
                })
                ->when(in_array($filter, ['peripherals', 'displays', 'audio'], true), function ($query) use ($filter) {
                    $types = match ($filter) {
                        'peripherals' => ['keyboard', 'mouse'],
                        'displays' => ['monitor'],
                        'audio' => ['headset', 'speaker'],
                    };

                    $query->where(function ($query) use ($filter, $types) {
                        $query->whereIn('type', $types)
                            ->orWhere(function ($query) use ($filter) {
                                $query->where('type', 'accessory')
                                    ->whereRaw('LOWER(category) = ?', [$filter]);
                            });
                    });
                })
                ->orderBy('name')
                ->get()
            : collect();

        return view('store.catalog', [
            'title' => 'Accessories',
            'description' => 'Peripherals, displays, and components.',
            'products' => $products,
            'filter' => $filter,
            'type' => 'accessory',
            'filters' => [
                'all' => 'All Accessories',
                'peripherals' => 'Peripherals',
                'displays' => 'Displays',
                'audio' => 'Audio',
                'components' => 'Components',
            ],
        ]);
    }

    public function categories(): View
    {
        $availableCategories = Schema::hasTable('products')
            ? Product::query()
                ->where('is_active', true)
                ->inStock()
                ->select('category')
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category')
            : collect();
        $categoryLinks = Schema::hasTable('categories')
            ? Category::query()
                ->whereIn('name', $availableCategories)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(function (Category $category): array {
                    return [
                        'label' => $this->categoryDisplayName($category->name),
                        'route' => $this->categoryRouteFor($category->name),
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'image_path' => $category->image_path,
                    ];
                })
                ->all()
            : [];

        return view('store.categories', ['categories' => $categoryLinks]);
    }

    public function category(string $categorySlug): View
    {
        $category = Schema::hasTable('categories')
            ? Category::query()->where('slug', $categorySlug)->first()
            : null;
        abort_if($category === null, 404);

        $products = Product::query()
            ->where('is_active', true)
            ->inStock()
            ->where('category', $category->name)
            ->orderBy('name')
            ->get();
        $label = $this->categoryDisplayName($category->name);

        return view('store.catalog', [
            'title' => $label,
            'description' => 'Explore available products in '.$label.'.',
            'products' => $products,
            'filter' => 'all',
            'type' => 'category',
            'categorySlug' => $categorySlug,
            'filters' => ['all' => 'All '.$label],
        ]);
    }

    public function specialOffers(): View
    {
        $products = Schema::hasTable('products')
            ? Product::query()->where('is_active', true)->inStock()->orderBy('price')->limit(12)->get()
            : collect();

        return view('store.catalog', [
            'title' => 'Special Offers',
            'description' => 'Popular picks and value bundles to save more.',
            'products' => $products,
            'filter' => 'all',
            'type' => 'offers',
            'filters' => ['all' => 'All Deals'],
        ]);
    }

    public function topSelling(): View
    {
        $bestSellers = SaleItem::query()
            ->select('product_id')
            ->selectRaw('SUM(quantity) AS units_sold')
            ->groupBy('product_id')
            ->orderByDesc('units_sold')
            ->limit(12)
            ->get();
        $productOrder = $bestSellers->pluck('product_id')->all();
        $products = $productOrder === []
            ? collect()
            : Product::query()
                ->whereIn('id', $productOrder)
                ->where('is_active', true)
                ->inStock()
                ->get()
                ->sortBy(fn (Product $product): int => array_search($product->id, $productOrder, true))
                ->values();

        return view('store.catalog', [
            'title' => 'Top Selling',
            'description' => 'Customer favorites based on recorded sales.',
            'products' => $products,
            'filter' => 'all',
            'type' => 'offers',
            'filters' => ['all' => 'Best Sellers'],
        ]);
    }
}
