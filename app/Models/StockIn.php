<?php

namespace App\Models;

use Database\Factories\StockInFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockIn extends Model
{
    /** @use HasFactory<StockInFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'supplier_name',
        'supplier_contact',
        'invoice_number',
        'received_at',
        'delivery_document_path',
        'before_photo_path',
        'after_photo_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockInItem::class);
    }
}
