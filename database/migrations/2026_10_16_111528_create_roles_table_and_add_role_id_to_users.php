<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->json('permissions');
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        $permissions = [
            'dashboard.view',
            'sales.create',
            'inventory.manage',
            'stock_in.manage',
            'quotations.manage',
            'builds.manage',
            'customers.manage',
            'credit.manage',
            'suppliers.manage',
            'warranty.verify',
            'transactions.manage',
            'employees.manage',
            'reports.view',
            'expenses.manage',
        ];

        DB::table('roles')->insert([
            ['name' => 'Owner', 'slug' => 'owner', 'permissions' => json_encode($permissions), 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cashier', 'slug' => 'cashier', 'permissions' => json_encode(['sales.create', 'quotations.manage', 'builds.manage', 'customers.manage', 'credit.manage']), 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inventory Staff', 'slug' => 'inventory', 'permissions' => json_encode(['dashboard.view', 'inventory.manage', 'stock_in.manage', 'suppliers.manage', 'reports.view']), 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Technical Staff', 'slug' => 'technical', 'permissions' => json_encode(['warranty.verify', 'transactions.manage']), 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Customer', 'slug' => 'customer', 'permissions' => json_encode([]), 'is_system' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
        });

        $ownerRoleId = DB::table('roles')->where('slug', 'owner')->value('id');
        $cashierRoleId = DB::table('roles')->where('slug', 'cashier')->value('id');
        $customerRoleId = DB::table('roles')->where('slug', 'customer')->value('id');

        DB::table('users')->whereRaw('LOWER(email) = ?', ['admin@davaobosscomputer.com'])->update(['role_id' => $ownerRoleId]);
        DB::table('users')->whereRaw('LOWER(email) = ?', ['employee@davaobosscomputer.com'])->update(['role_id' => $cashierRoleId]);
        DB::table('users')->whereNull('role_id')->update(['role_id' => $customerRoleId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn('is_active');
        });

        Schema::dropIfExists('roles');
    }
};
