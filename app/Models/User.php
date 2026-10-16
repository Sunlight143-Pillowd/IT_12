<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission(string $permission): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $role = $this->role;
        if ($role !== null) {
            return in_array($permission, $role->permissions ?? [], true);
        }

        if (strtolower($this->email) === 'admin@davaobosscomputer.com') {
            return true;
        }

        return strtolower($this->email) === 'employee@davaobosscomputer.com'
            && in_array($permission, [
                'dashboard.view',
                'sales.create',
                'quotations.manage',
                'builds.manage',
                'customers.manage',
                'credit.manage',
            ], true);
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'owner'
            || strtolower($this->email) === 'admin@davaobosscomputer.com';
    }

    public function isEmployee(): bool
    {
        return ($this->role !== null && $this->role->slug !== 'customer')
            || in_array(strtolower($this->email), [
                'admin@davaobosscomputer.com',
                'employee@davaobosscomputer.com',
            ], true);
    }

    public function canManageOrders(): bool
    {
        return $this->hasPermission('dashboard.view');
    }

    public function isStaff(): bool
    {
        return $this->canManageOrders();
    }
}
