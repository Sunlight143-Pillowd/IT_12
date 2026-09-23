<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PcBuildItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pc_build_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'subtotal' => 'float',
    ];

    public function build(): BelongsTo
    {
        return $this->belongsTo(PcBuild::class, 'pc_build_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
