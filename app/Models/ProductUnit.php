<?php

namespace App\Models;

use Database\Factories\ProductUnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUnit extends Model
{
    /** @use HasFactory<ProductUnitFactory> */
    use HasFactory;

    protected $fillable = [
        'stock_in_item_id',
        'product_id',
        'sale_item_id',
        'serial_number',
        'warranty_months',
        'lot_code',
        'manufactured_at',
        'warranty_ends_at',
        'status',
        'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'warranty_months' => 'integer',
            'manufactured_at' => 'date',
            'warranty_ends_at' => 'date',
            'sold_at' => 'datetime',
        ];
    }

    public function stockInItem(): BelongsTo
    {
        return $this->belongsTo(StockInItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function saleItem(): BelongsTo
    {
        return $this->belongsTo(SaleItem::class);
    }
}
