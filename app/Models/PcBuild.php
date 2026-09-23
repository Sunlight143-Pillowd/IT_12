<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PcBuild extends Model
{
    use HasFactory;

    protected $fillable = [
        'build_number',
        'customer_name',
        'customer_email',
        'employee_id',
        'status',
        'total_cost',
        'notes',
        'product_id',
        'sold_at',
        'expires_at',
    ];

    protected $casts = [
        'total_cost' => 'float',
        'sold_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PcBuildItem::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(StockReservation::class);
    }
}
