<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('store_orders', function (Blueprint $table) {
            Schema::table('store_orders', function (Blueprint $table) {
                $table->string('customer_company')->nullable();
                $table->text('billing_address')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_orders', function (Blueprint $table) {
            Schema::table('store_orders', function (Blueprint $table) {
                $table->dropColumn(['customer_company', 'billing_address']);
            });
        });
    }
};
