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
            $table->string('shipping_zone', 40)->nullable();
            $table->decimal('shipping_distance_km', 8, 2)->nullable();
            $table->decimal('shipping_fee', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_zone',
                'shipping_distance_km',
                'shipping_fee',
            ]);
        });
    }
};
