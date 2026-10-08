<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_orders', function (Blueprint $table) {
            $table->string('customer_phone', 40)->nullable();
            $table->string('payment_method', 40)->default('cash');
            $table->string('fulfillment_method', 40)->default('pickup');
            $table->text('delivery_address')->nullable();
        });

        Schema::table('pc_builds', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('employee_id');
            $table->string('product_photo_path')->nullable();
            $table->string('before_photo_path')->nullable();
            $table->string('after_photo_path')->nullable();
            $table->timestamp('stock_deducted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pc_builds', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn([
                'product_photo_path',
                'before_photo_path',
                'after_photo_path',
                'stock_deducted_at',
            ]);
        });

        Schema::table('store_orders', function (Blueprint $table) {
            $table->dropColumn([
                'customer_phone',
                'payment_method',
                'fulfillment_method',
                'delivery_address',
            ]);
        });
    }
};
