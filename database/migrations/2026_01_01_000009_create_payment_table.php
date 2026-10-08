<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_table', function (Blueprint $table) {
            $table->id('Payment_ID');
            $table->foreignId('orderitem_ID')->constrained('orders_items', 'orderitem_ID');
            $table->foreignId('order_ID')->constrained('orders_table', 'Order_Id');
            $table->decimal('amount', 10, 2);
            $table->timestamp('payment_date')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_table');
    }
};
