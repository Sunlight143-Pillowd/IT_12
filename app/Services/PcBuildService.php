<?php

namespace App\Services;

use App\Models\PcBuild;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockReservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
            'other' => 'Other Components',
        ];
    }

    public function availableProducts(?int $pcBuildId = null): Collection
    {
        return Product::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($pcBuildId): void {
                $query->inStock();

                if ($pcBuildId !== null) {
                    $query->orWhereHas('reservations', fn (Builder $reservations) => $reservations
                        ->where('pc_build_id', $pcBuildId)
                        ->where('status', 'active'));
                }
            })
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

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
                'customer_name' => trim((string) ($payload['customer_name'] ?? $user->name)),
                'customer_email' => trim((string) ($payload['customer_email'] ?? $user->email)) ?: $user->email,
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
            $quantities = $lockedBuild->items->groupBy('product_id')->map(
                fn ($items): int => (int) $items->sum('quantity')
            );
            $products = Product::query()
                ->whereIn('id', $quantities->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($status === 'accepted' && $lockedBuild->stock_deducted_at === null) {
                foreach ($quantities as $productId => $quantity) {
                    $product = $products->get($productId);
                    if (! $product || ! $product->is_active || $this->availableQuantity($product) < $quantity) {
                        throw ValidationException::withMessages([
                            'status' => ['Unable to accept the build: '.($product?->name ?? 'A selected product').' no longer has enough stock.'],
                        ]);
                    }
                }

                foreach ($quantities as $productId => $quantity) {
                    Product::query()->whereKey($productId)->decrement('stock_quantity', $quantity);
                }
                $lockedBuild->stock_deducted_at = now();
            }

            if ($status === 'cancelled' && $lockedBuild->stock_deducted_at !== null) {
                foreach ($quantities as $productId => $quantity) {
                    Product::query()->whereKey($productId)->increment('stock_quantity', $quantity);
                }
                $lockedBuild->stock_deducted_at = null;
            }

            $lockedBuild->status = $status;
            $lockedBuild->save();
        });
    }

    public function createBuild(User $user, array $payload): PcBuild
    {
        $items = $this->normalizeItems($payload['items'] ?? []);

        if ($items === []) {
            throw ValidationException::withMessages([
                'items' => ['Select at least one component to build a PC.'],
            ]);
        }

        return DB::transaction(function () use ($user, $payload, $items) {
            $products = Product::query()
                ->whereIn('id', array_column($items, 'product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;
            $selected = [];

            foreach ($items as $entry) {
                $product = $products->get($entry['product_id']);

                if (! $product || ! $product->is_active) {
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

            if ($build->user_id === null) {
                $this->createStorefrontProduct($build);
            }

            return $build->fresh(['items.product', 'reservations.product']);
        });
    }

    public function updateBuildItems(PcBuild $build, array $payload): PcBuild
    {
        $items = $this->normalizeItems($payload['items'] ?? []);

        if ($items === []) {
            throw ValidationException::withMessages([
                'items' => ['Select at least one component to update the build.'],
            ]);
        }

        return DB::transaction(function () use ($build, $items): PcBuild {
            $lockedBuild = PcBuild::query()->whereKey($build->id)->lockForUpdate()->firstOrFail();
            if (! in_array($lockedBuild->status, ['draft', 'reserved', 'pending'], true)
                || $lockedBuild->stock_deducted_at !== null) {
                throw ValidationException::withMessages([
                    'items' => ['This PC build can no longer be edited after stock has been committed.'],
                ]);
            }

            $products = Product::query()
                ->whereIn('id', array_column($items, 'product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;
            $selected = [];

            foreach ($items as $entry) {
                $product = $products->get($entry['product_id']);

                if (! $product || ! $product->is_active) {
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

                $available = $this->availableQuantity(
                    $product,
                    $lockedBuild->user_id === null ? $lockedBuild->id : null,
                );
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

            $lockedBuild->items()->delete();
            $lockedBuild->reservations()->delete();

            foreach ($selected as $entry) {
                $lockedBuild->items()->create([
                    'product_id' => $entry['product_id'],
                    'quantity' => $entry['quantity'],
                    'unit_price' => $entry['unit_price'],
                    'subtotal' => $entry['subtotal'],
                ]);

                if ($lockedBuild->user_id === null) {
                    $lockedBuild->reservations()->create([
                        'product_id' => $entry['product_id'],
                        'quantity' => $entry['quantity'],
                        'status' => 'active',
                        'expires_at' => now()->addMinutes(30),
                    ]);
                }
            }

            $lockedBuild->update([
                'total_cost' => $total,
                'status' => $lockedBuild->user_id !== null ? 'pending' : 'reserved',
            ]);

            if ($lockedBuild->user_id === null) {
                $this->createStorefrontProduct($lockedBuild);
            }

            return $lockedBuild->fresh(['items.product', 'reservations.product']);
        });
    }

    public function cancel(PcBuild $build): void
    {
        DB::transaction(function () use ($build) {
            if ($build->product_id) {
                $build->update(['product_id' => null]);
            }

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
            $lockedBuild = PcBuild::query()->whereKey($build->id)->lockForUpdate()->firstOrFail();
            if (! in_array($lockedBuild->status, ['draft', 'reserved'], true)) {
                throw ValidationException::withMessages([
                    'status' => ['This PC build cannot be sold in its current status.'],
                ]);
            }

            $lockedBuild->load('items.product', 'reservations');
            $quantities = $lockedBuild->items->groupBy('product_id')->map(
                fn ($items): int => (int) $items->sum('quantity')
            );
            $products = Product::query()
                ->whereIn('id', $quantities->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);
                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => ['One of the selected products is no longer available.'],
                    ]);
                }

                $reservedForOtherBuilds = StockReservation::query()
                    ->where('product_id', $product->id)
                    ->where('status', 'active')
                    ->where('pc_build_id', '!=', $lockedBuild->id)
                    ->sum('quantity');

                if ($product->stock_quantity - $reservedForOtherBuilds < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["Unable to sell {$product->name}: not enough stock is currently available."],
                    ]);
                }
            }

            $bundleProduct = null;
            if ($lockedBuild->product_id !== null) {
                $bundleProduct = Product::query()->lockForUpdate()->findOrFail($lockedBuild->product_id);
                if (! $bundleProduct->is_active || $bundleProduct->stock_quantity < 1) {
                    throw ValidationException::withMessages([
                        'status' => ['This custom PC build is no longer available to sell.'],
                    ]);
                }
            }

            $sale = Sale::create([
                'employee_id' => $user->id,
                'customer_name' => $lockedBuild->customer_name,
                'total_amount' => (float) $lockedBuild->total_cost,
            ]);

            foreach ($lockedBuild->items as $item) {
                $product = $products->get($item->product_id);

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

            foreach ($lockedBuild->reservations as $reservation) {
                if ($reservation->status === 'active') {
                    $reservation->update([
                        'status' => 'consumed',
                        'consumed_at' => now(),
                    ]);
                }
            }

            $bundleProduct?->decrement('stock_quantity');
            $lockedBuild->update([
                'status' => 'sold',
                'sold_at' => now(),
            ]);

            return $sale;
        });
    }

    public function deleteBuild(PcBuild $build): void
    {
        DB::transaction(function () use ($build): void {
            $lockedBuild = PcBuild::query()->whereKey($build->id)->lockForUpdate()->firstOrFail();
            $lockedBuild->load('items');

            if ($lockedBuild->stock_deducted_at !== null) {
                $quantities = $lockedBuild->items->groupBy('product_id')->map(
                    fn ($items): int => (int) $items->sum('quantity')
                );
                $products = Product::query()
                    ->whereIn('id', $quantities->keys())
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($quantities as $productId => $quantity) {
                    $product = $products->get($productId)
                        ?? Product::query()->whereKey($productId)->firstOrFail();
                    $product->increment('stock_quantity', $quantity);
                }
            }

            $lockedBuild->reservations()->where('status', 'active')->update([
                'status' => 'released',
                'released_at' => now(),
            ]);

            if ($lockedBuild->product_id) {
                $lockedBuild->product()->delete();
            }

            $lockedBuild->items()->delete();
            $lockedBuild->reservations()->delete();
            $lockedBuild->delete();
        });
    }

    public function availableQuantity(Product $product, ?int $exceptBuildId = null): int
    {
        $reservations = StockReservation::query()
            ->where('product_id', $product->id)
            ->where('status', 'active');

        if ($exceptBuildId !== null) {
            $reservations->where('pc_build_id', '!=', $exceptBuildId);
        }

        $held = $reservations->sum('quantity');

        return max(0, (int) $product->stock_quantity - (int) $held);
    }

    public function availableQuantities(Collection $products, ?int $exceptBuildId = null): array
    {
        if ($products->isEmpty()) {
            return [];
        }

        $reservations = StockReservation::query()
            ->selectRaw('product_id, SUM(quantity) as reserved_quantity')
            ->whereIn('product_id', $products->modelKeys())
            ->where('status', 'active')
            ->groupBy('product_id');

        if ($exceptBuildId !== null) {
            $reservations->where('pc_build_id', '!=', $exceptBuildId);
        }

        $reservedQuantities = $reservations->pluck('reserved_quantity', 'product_id');

        return $products->mapWithKeys(static fn (Product $product): array => [
            $product->id => max(0, (int) $product->stock_quantity - (int) $reservedQuantities->get($product->id, 0)),
        ])->all();
    }

    protected function normalizeItems(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = 1;

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $normalized[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        $productIds = array_column($normalized, 'product_id');
        if (count($productIds) !== count(array_unique($productIds))) {
            throw ValidationException::withMessages([
                'items' => ['Select each product only once per build.'],
            ]);
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
        if ($build->user_id !== null) {
            return;
        }

        $build->load('items.product');

        $description = $build->items
            ->map(fn ($item) => $item->product->name.($item->quantity > 1 ? ' x'.$item->quantity : ''))
            ->implode(' • ');

        $description = $description !== '' ? $description : 'Custom-built desktop configuration.';

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
