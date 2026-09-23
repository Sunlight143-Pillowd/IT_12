<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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
        $normalized = strtolower(trim($category));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        $routeMap = [
            'gaming' => ['route' => 'store.desktops', 'filter' => 'gaming'],
            'ready to ship' => ['route' => 'store.desktops', 'filter' => 'ready-to-ship'],
            'ready-to-ship' => ['route' => 'store.desktops', 'filter' => 'ready-to-ship'],
            'workstation' => ['route' => 'store.desktops', 'filter' => 'workstation'],
            'thin & light' => ['route' => 'store.laptops', 'filter' => 'thin-and-light'],
            'thin-and-light' => ['route' => 'store.laptops', 'filter' => 'thin-and-light'],
            'performance' => ['route' => 'store.laptops', 'filter' => 'performance'],
            'gpu' => ['route' => 'store.accessories', 'filter' => 'components'],
            'graphics card' => ['route' => 'store.accessories', 'filter' => 'components'],
            'graphics cards' => ['route' => 'store.accessories', 'filter' => 'components'],
            'cpu' => ['route' => 'store.accessories', 'filter' => 'components'],
            'case' => ['route' => 'store.accessories', 'filter' => 'components'],
            'fans' => ['route' => 'store.accessories', 'filter' => 'components'],
            'cpu cooler' => ['route' => 'store.accessories', 'filter' => 'components'],
            'ssd' => ['route' => 'store.accessories', 'filter' => 'components'],
            'ram' => ['route' => 'store.accessories', 'filter' => 'components'],
            'memory modules' => ['route' => 'store.accessories', 'filter' => 'components'],
            'power supply' => ['route' => 'store.accessories', 'filter' => 'components'],
            'power supplies' => ['route' => 'store.accessories', 'filter' => 'components'],
            'motherboard' => ['route' => 'store.accessories', 'filter' => 'components'],
            'motherboards' => ['route' => 'store.accessories', 'filter' => 'components'],
            'network attached storage' => ['route' => 'store.accessories', 'filter' => 'components'],
            'network attached storage(nas)' => ['route' => 'store.accessories', 'filter' => 'components'],
            'printers' => ['route' => 'store.accessories', 'filter' => 'components'],
            'routers' => ['route' => 'store.accessories', 'filter' => 'components'],
            'processors' => ['route' => 'store.accessories', 'filter' => 'components'],
            'hard drives' => ['route' => 'store.accessories', 'filter' => 'components'],
            'flash drives' => ['route' => 'store.accessories', 'filter' => 'components'],
            'external hard drives' => ['route' => 'store.accessories', 'filter' => 'components'],
            'external ssd' => ['route' => 'store.accessories', 'filter' => 'components'],
            'micro sd' => ['route' => 'store.accessories', 'filter' => 'components'],
            'monitor' => ['route' => 'store.accessories', 'filter' => 'displays'],
            'monitors' => ['route' => 'store.accessories', 'filter' => 'displays'],
            'display' => ['route' => 'store.accessories', 'filter' => 'displays'],
            'displays' => ['route' => 'store.accessories', 'filter' => 'displays'],
            'mouse' => ['route' => 'store.accessories', 'filter' => 'peripherals'],
            'mice' => ['route' => 'store.accessories', 'filter' => 'peripherals'],
            'keyboard' => ['route' => 'store.accessories', 'filter' => 'peripherals'],
            'keyboards' => ['route' => 'store.accessories', 'filter' => 'peripherals'],
            'peripherals' => ['route' => 'store.accessories', 'filter' => 'peripherals'],
            'speaker' => ['route' => 'store.accessories', 'filter' => 'audio'],
            'speakers' => ['route' => 'store.accessories', 'filter' => 'audio'],
            'audio' => ['route' => 'store.accessories', 'filter' => 'audio'],
            'headset' => ['route' => 'store.accessories', 'filter' => 'audio'],
            'headsets' => ['route' => 'store.accessories', 'filter' => 'audio'],
        ];

        if (isset($routeMap[$normalized])) {
            $config = $routeMap[$normalized];

            return route($config['route'], ['filter' => $config['filter']]);
        }

        return route('store.desktops', ['filter' => 'gaming']);
    }

    private function categoryDisplayName(string $category): string
    {
        $normalized = strtolower(trim($category));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        $displayMap = [
            'gaming' => 'Gaming',
            'workstation' => 'Workstation',
            'ready to ship' => 'Ready to Ship',
            'ready-to-ship' => 'Ready to Ship',
            'thin & light' => 'Thin & Light',
            'thin-and-light' => 'Thin & Light',
            'performance' => 'Performance',
            'gpu' => 'GPU',
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
        $categoryCards = [
            ['label' => 'Gaming Desktops', 'route' => route('store.desktops', ['filter' => 'gaming']), 'type' => 'desktop'],
            ['label' => 'Ready to Ship', 'route' => route('store.desktops', ['filter' => 'ready-to-ship']), 'type' => 'desktop'],
            ['label' => 'Gaming Laptops', 'route' => route('store.laptops', ['filter' => 'performance']), 'type' => 'laptop'],
            ['label' => 'Workstation', 'route' => route('store.desktops', ['filter' => 'workstation']), 'type' => 'desktop'],
            ['label' => 'Categories', 'route' => route('store.categories'), 'type' => 'category'],
        ];

        $featured = Schema::hasTable('products')
            ? Product::query()->where('is_active', true)->orderBy('price', 'desc')->limit(4)->get()
            : collect();

        return view('welcome', compact('categoryCards', 'featured'));
    }

    public function desktops(Request $request): View
    {
        $filter = $request->query('filter', 'all');
        $allowedFilters = ['all', 'ready-to-ship', 'gaming', 'workstation'];
        $filter = in_array($filter, $allowedFilters, true) ? $filter : 'all';

        $products = Schema::hasTable('products')
            ? Product::query()
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
                'ready-to-ship' => 'Ready to Ship',
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
                ->where('type', 'accessory')
                ->when($filter !== 'all', function ($query) use ($filter) {
                    $query->where('category', match ($filter) {
                        'peripherals' => 'peripherals',
                        'displays' => 'displays',
                        'audio' => 'audio',
                        'components' => 'components',
                        default => null,
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
        $categories = Schema::hasTable('products')
            ? Product::query()
                ->select('category')
                ->whereNotNull('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->toArray()
            : [];

        $categoryLinks = array_map(function ($category) {
            return [
                'label' => $this->categoryDisplayName($category),
                'route' => $this->categoryRouteFor($category),
            ];
        }, $categories);

        return view('store.categories', ['categories' => $categoryLinks]);
    }

    public function specialOffers(): View
    {
        $products = Schema::hasTable('products')
            ? Product::query()->where('is_active', true)->orderBy('price', 'asc')->limit(12)->get()
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
}
