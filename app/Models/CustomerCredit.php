<?php

namespace App\Models;

use Database\Factories\CustomerCreditFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerCredit extends Model
{
    /** @use HasFactory<CustomerCreditFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'sale_id',
        'credit_date',
        'description',
        'amount_due',
        'balance_due',
    ];

    protected function casts(): array
    {
        return [
            'credit_date' => 'date',
            'amount_due' => 'decimal:2',
            'balance_due' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CreditPayment::class);
    }
}
