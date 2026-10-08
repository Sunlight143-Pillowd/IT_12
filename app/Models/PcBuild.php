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
<<<<<<< HEAD
        'user_id',
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
        'status',
        'total_cost',
        'notes',
        'product_id',
        'sold_at',
        'expires_at',
<<<<<<< HEAD
        'product_photo_path',
        'before_photo_path',
        'after_photo_path',
        'stock_deducted_at',
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
    ];

    protected $casts = [
        'total_cost' => 'float',
        'sold_at' => 'datetime',
        'expires_at' => 'datetime',
<<<<<<< HEAD
        'stock_deducted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

=======
    ];

>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
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
