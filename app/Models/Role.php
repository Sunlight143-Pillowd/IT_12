<?php

namespace App\Models;

use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /** @use HasFactory<RoleFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'permissions', 'is_system'];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_system' => 'boolean',
        ];
    }

    public static function permissionOptions(): array
    {
        return [
            'dashboard.view' => 'View dashboard',
            'sales.create' => 'Process POS sales',
            'inventory.manage' => 'Manage products and inventory',
            'stock_in.manage' => 'Record stock-in deliveries',
            'quotations.manage' => 'Prepare quotations',
            'builds.manage' => 'Manage custom PC builds',
            'customers.manage' => 'Manage customer records and feedback',
            'credit.manage' => 'Manage customer credit',
            'suppliers.manage' => 'Manage suppliers',
            'warranty.verify' => 'Verify product warranty',
            'transactions.manage' => 'Update service transaction status',
            'employees.manage' => 'Manage employee accounts and roles',
            'reports.view' => 'View reports',
            'expenses.manage' => 'Record business expenses',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
