<?php

namespace App\Models;

use Database\Factories\ServiceCaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceCase extends Model
{
    /** @use HasFactory<ServiceCaseFactory> */
    use HasFactory;

    protected $fillable = [
        'case_number',
        'customer_id',
        'product_unit_id',
        'sale_id',
        'created_by',
        'case_type',
        'status',
        'description',
        'resolution',
        'completed_at',
    ];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function productUnit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
