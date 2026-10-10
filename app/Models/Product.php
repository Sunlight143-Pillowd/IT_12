<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'category',
        'price',
        'stock_quantity',
        'low_stock_threshold',
        'stock_location',
        'size',
        'tags',
        'image_path',
        'before_image_path',
        'after_image_path',
        'requires_serial',
        'warranty_months',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requires_serial' => 'boolean',
            'warranty_months' => 'integer',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(StockReservation::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function stockInItems(): HasMany
    {
        return $this->hasMany(StockInItem::class);
    }

    #[Scope]
    protected function inStock(Builder $query): void
    {
        $query
            ->where('products.stock_quantity', '>', 0)
            ->whereRaw(
                'products.stock_quantity > (SELECT COALESCE(SUM(stock_reservations.quantity), 0) FROM stock_reservations WHERE stock_reservations.product_id = products.id AND stock_reservations.status = ?)',
                ['active'],
            );
    }

    public function availableStock(): int
    {
        $held = $this->reservations()->where('status', 'active')->sum('quantity');

        return max(0, (int) $this->stock_quantity - (int) $held);
    }
}
