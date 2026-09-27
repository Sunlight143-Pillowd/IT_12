<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders_items', function (Blueprint $table) {
            $table->id('orderitem_ID');
            $table->foreignId('order_ID')->constrained('orders_table', 'Order_Id');
            $table->foreignId('product_ID')->constrained('products_table', 'product_ID');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_Price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders_items');
    }
};
