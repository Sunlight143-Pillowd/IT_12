<?php

namespace App\Services;

use App\Models\PcBuild;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockReservation;
use App\Models\User;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Collection;
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PcBuildService
{
    public function componentGroups(): array
    {
        return [
            'cpu' => 'CPU',
            'motherboard' => 'Motherboard',
            'gpu' => 'GPU',
            'ram' => 'RAM',
            'storage' => 'Storage',
            'power_supply' => 'PSU',
            'case' => 'PC Case',
            'cpu_cooler' => 'CPU Cooler',
<<<<<<< HEAD
            'other' => 'Other Components',
        ];
    }

    public function availableProducts(): Collection
=======
        ];
    }

    public function availableProducts()
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
    {
        return Product::query()
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

<<<<<<< HEAD
    public function componentGroupForProduct(Product $product): ?string
    {
        $type = strtolower(str_replace([' ', '-'], '_', (string) $product->type));
        $category = strtolower(str_replace([' ', '-'], '_', (string) $product->category));
        $aliases = [
            'psu' => 'power_supply',
            'power_supplies' => 'power_supply',
            'graphics_card' => 'gpu',
            'graphics_cards' => 'gpu',
            'mother_board' => 'motherboard',
            'motherboards' => 'motherboard',
            'pc_case' => 'case',
            'cases' => 'case',
            'cooler' => 'cpu_cooler',
            'processor' => 'cpu',
            'processors' => 'cpu',
            'memory' => 'ram',
            'memory_modules' => 'ram',
            'ssd' => 'storage',
            'hdd' => 'storage',
            'hard_drive' => 'storage',
            'hard_drives' => 'storage',
        ];
        $type = $aliases[$type] ?? $type;
        $category = $aliases[$category] ?? $category;

        foreach (array_keys($this->componentGroups()) as $group) {
            if ($group === 'other') {
                continue;
            }

            if ($type === $group || $category === $group) {
                return $group;
            }
        }

        $otherComponentTypes = ['fan', 'fans', 'networking', 'storage', 'ssd', 'hdd', 'accessory'];
        $otherComponentCategories = ['component', 'components', 'fan', 'fans', 'networking', 'storage', 'ssd', 'hdd'];

        if (in_array($type, $otherComponentTypes, true)
            && ($type !== 'accessory' || in_array($category, $otherComponentCategories, true))) {
            return 'other';
        }

        return null;
    }

    public function createCustomerBuild(User $user, array $payload): PcBuild
    {
        $requestedItems = [];

        foreach (($payload['items'] ?? []) as $group => $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            if ($productId < 1) {
                continue;
            }

            $requestedItems[] = [
                'group' => (string) $group,
                'product_id' => $productId,
                'quantity' => 1,
            ];
        }

        if ($requestedItems === []) {
            throw ValidationException::withMessages([
                'items' => ['Select at least one component to submit your build.'],
            ]);
        }

        return DB::transaction(function () use ($user, $requestedItems): PcBuild {
            $products = Product::query()
                ->whereIn('id', array_column($requestedItems, 'product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $buildItems = [];
            $total = 0;

            foreach ($requestedItems as $entry) {
                $product = $products->get($entry['product_id']);
                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => ['One of the selected products is no longer available.'],
                    ]);
                }

                $productGroup = $this->componentGroupForProduct($product);
                if (($entry['group'] === 'other' && $productGroup !== 'other')
                    || ($entry['group'] !== 'other' && $entry['group'] !== $productGroup)) {
                    throw ValidationException::withMessages([
                        'items' => ["{$product->name} is not available in the selected component category."],
                    ]);
                }

                $quantity = $entry['quantity'];
                if ($quantity < 1 || $this->availableQuantity($product) < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["{$product->name} does not have enough stock for this build."],
                    ]);
                }

                $subtotal = (float) $product->price * $quantity;
                $buildItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => (float) $product->price,
                    'subtotal' => $subtotal,
                ];
                $total += $subtotal;
            }

            $build = PcBuild::create([
                'build_number' => $this->nextBuildNumber(),
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'user_id' => $user->id,
                'status' => 'pending',
                'total_cost' => $total,
            ]);

            foreach ($buildItems as $item) {
                $build->items()->create($item);
            }

            return $build->fresh(['items.product']);
        });
    }

    public function updateCustomerBuildStatus(PcBuild $build, string $status): void
    {
        $transitions = [
            'pending' => ['accepted', 'cancelled'],
            'accepted' => ['building', 'cancelled'],
            'building' => ['testing', 'cancelled'],
            'testing' => ['ready', 'cancelled'],
            'ready' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        DB::transaction(function () use ($build, $status, $transitions): void {
            $lockedBuild = PcBuild::query()->whereKey($build->id)->lockForUpdate()->firstOrFail();
            abort_if($lockedBuild->user_id === null, 404);
            abort_unless(in_array($status, $transitions[$lockedBuild->status] ?? [], true), 409, 'This build cannot move to that status.');

            $lockedBuild->load('items');
            if ($status === 'accepted') {
                foreach ($lockedBuild->items as $item) {
                    $product = Product::query()->whereKey($item->product_id)->lockForUpdate()->firstOrFail();
                    if (! $product->is_active || $this->availableQuantity($product) < $item->quantity) {
                        throw ValidationException::withMessages([
                            'status' => ["Unable to accept the build: {$product->name} no longer has enough stock."],
                        ]);
                    }
                }

                foreach ($lockedBuild->items as $item) {
                    Product::query()->whereKey($item->product_id)->decrement('stock_quantity', $item->quantity);
                }
                $lockedBuild->stock_deducted_at = now();
            }

            if ($status === 'cancelled' && $lockedBuild->stock_deducted_at !== null) {
                foreach ($lockedBuild->items as $item) {
                    Product::query()->whereKey($item->product_id)->lockForUpdate()->increment('stock_quantity', $item->quantity);
                }
                $lockedBuild->stock_deducted_at = null;
            }

            $lockedBuild->status = $status;
            $lockedBuild->save();
        });
    }

=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
    public function createBuild(User $user, array $payload): PcBuild
    {
        $items = $this->normalizeItems($payload['items'] ?? []);

        if ($items === []) {
            throw ValidationException::withMessages([
                'items' => ['Select at least one component to build a PC.'],
            ]);
        }

        return DB::transaction(function () use ($user, $payload, $items) {
            $products = Product::whereIn('id', array_column($items, 'product_id'))
                ->get()
                ->keyBy('id');

            $total = 0;
            $selected = [];

            foreach ($items as $entry) {
                $product = $products->get($entry['product_id']);

                if (! $product) {
                    throw ValidationException::withMessages([
                        'items' => ['One of the selected products is no longer available.'],
                    ]);
                }

                $quantity = (int) $entry['quantity'];

                if ($quantity <= 0) {
                    throw ValidationException::withMessages([
                        'items' => ['Each selected component must have a quantity greater than 0.'],
                    ]);
                }

                $available = $this->availableQuantity($product);
                if ($available < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["{$product->name} only has {$available} unit(s) available."],
                    ]);
                }

                $selected[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => (float) $product->price,
                    'subtotal' => (float) $product->price * $quantity,
                ];

                $total += (float) $product->price * $quantity;
            }

            $build = PcBuild::create([
                'build_number' => $this->nextBuildNumber(),
                'customer_name' => trim((string) ($payload['customer_name'] ?? '')),
                'customer_email' => trim((string) ($payload['customer_email'] ?? '')) ?: null,
                'employee_id' => $user->id,
                'status' => 'reserved',
                'total_cost' => $total,
                'notes' => $payload['notes'] ?? null,
                'expires_at' => now()->addMinutes(30),
            ]);

            foreach ($selected as $entry) {
                $build->items()->create([
                    'product_id' => $entry['product_id'],
                    'quantity' => $entry['quantity'],
                    'unit_price' => $entry['unit_price'],
                    'subtotal' => $entry['subtotal'],
                ]);

                $build->reservations()->create([
                    'product_id' => $entry['product_id'],
                    'quantity' => $entry['quantity'],
                    'status' => 'active',
                    'expires_at' => now()->addMinutes(30),
                ]);
            }

            $this->createStorefrontProduct($build);

            return $build->fresh(['items.product', 'reservations.product']);
        });
    }

    public function cancel(PcBuild $build): void
    {
        DB::transaction(function () use ($build) {
            $build->reservations()->where('status', 'active')->update([
                'status' => 'released',
                'released_at' => now(),
            ]);

            $build->update([
                'status' => 'cancelled',
                'expires_at' => now(),
            ]);
        });
    }

    public function expire(PcBuild $build): void
    {
        DB::transaction(function () use ($build) {
            $build->reservations()->where('status', 'active')->update([
                'status' => 'expired',
                'released_at' => now(),
            ]);

            $build->update([
                'status' => 'expired',
                'expires_at' => now(),
            ]);
        });
    }

    public function sell(PcBuild $build, User $user): Sale
    {
        return DB::transaction(function () use ($build, $user) {
            if (! in_array($build->status, ['draft', 'reserved'], true)) {
                throw ValidationException::withMessages([
                    'status' => ['This PC build cannot be sold in its current status.'],
                ]);
            }

            $build->load('items.product', 'reservations');

            foreach ($build->reservations as $reservation) {
                if ($reservation->status !== 'active') {
                    continue;
                }

                $product = Product::whereKey($reservation->product_id)->lockForUpdate()->firstOrFail();
                $reservedForOtherBuilds = StockReservation::query()
                    ->where('product_id', $product->id)
                    ->where('status', 'active')
                    ->where('pc_build_id', '!=', $build->id)
                    ->sum('quantity');

                if ($product->stock_quantity - $reservedForOtherBuilds < $reservation->quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["Unable to sell {$product->name}: not enough stock is currently available."],
                    ]);
                }
            }

            $sale = Sale::create([
                'employee_id' => $user->id,
                'customer_name' => $build->customer_name,
                'total_amount' => (float) $build->total_cost,
            ]);

            foreach ($build->items as $item) {
                $product = Product::query()->whereKey($item->product_id)->lockForUpdate()->firstOrFail();

                if ($product->requires_serial) {
                    $units = ProductUnit::query()
                        ->where('product_id', $product->id)
                        ->where('status', 'in_stock')
                        ->orderBy('id')
                        ->limit($item->quantity)
                        ->lockForUpdate()
                        ->get();

                    if ($units->count() !== $item->quantity) {
                        throw ValidationException::withMessages([
                            'items' => ["Unable to sell {$product->name}: not enough serialized units are available."],
                        ]);
                    }
                }

                $product->decrement('stock_quantity', $item->quantity);

                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => (float) $item->unit_price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                ]);

                if ($product->requires_serial) {
                    foreach ($units as $unit) {
                        $unit->update([
                            'sale_item_id' => $saleItem->id,
                            'status' => 'sold',
                            'sold_at' => now(),
                        ]);
                    }
                }
            }

            foreach ($build->reservations as $reservation) {
                if ($reservation->status === 'active') {
                    $reservation->update([
                        'status' => 'consumed',
                        'consumed_at' => now(),
                    ]);
                }
            }

            $build->update([
                'status' => 'sold',
                'sold_at' => now(),
            ]);

            return $sale;
        });
    }

    public function availableQuantity(Product $product): int
    {
        $held = StockReservation::query()
            ->where('product_id', $product->id)
            ->where('status', 'active')
            ->sum('quantity');

        return max(0, (int) $product->stock_quantity - (int) $held);
    }

    protected function normalizeItems(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $productId = (int) ($item['product_id'] ?? 0);
<<<<<<< HEAD
            $quantity = 1;
=======
            $quantity = (int) ($item['quantity'] ?? 1);
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $normalized[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        return $normalized;
    }

    protected function nextBuildNumber(): string
    {
        $last = PcBuild::query()->orderByDesc('id')->value('build_number');
        $sequence = 1;

        if ($last && preg_match('/PC-(\d+)/', $last, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return 'PC-'.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }

    protected function createStorefrontProduct(PcBuild $build): void
    {
        $build->load('items.product');

        $description = $build->items
<<<<<<< HEAD
            ->map(fn ($item) => $item->product->name.($item->quantity > 1 ? ' x'.$item->quantity : ''))
            ->implode(' • ');

        $description = $description !== '' ? $description : 'Custom-built desktop configuration.';
=======
            ->map(fn ($item) => "- {$item->product->name} x{$item->quantity} ({$item->product->category})")
            ->implode("\n");
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce

        $productName = 'Custom PC Build '.$build->build_number;

        $product = Product::query()->firstOrCreate(
            ['name' => $productName],
            [
                'slug' => Str::slug($productName).'-'.$build->id,
                'type' => 'desktop',
                'category' => 'Custom Build',
                'price' => (int) $build->total_cost,
                'stock_quantity' => 1,
                'low_stock_threshold' => 0,
                'stock_location' => 'warehouse',
                'description' => $description,
                'is_active' => true,
            ]
        );

        $product->update([
            'description' => $description,
            'price' => (int) $build->total_cost,
        ]);

        $build->update(['product_id' => $product->id]);
    }
}
