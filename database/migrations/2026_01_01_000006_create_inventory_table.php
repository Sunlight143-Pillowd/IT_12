<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_table', function (Blueprint $table) {
            $table->id('Inventory_ID');
            $table->foreignId('Product_ID')->constrained('products_table', 'product_ID');
            $table->integer('Current_Stock');
            $table->string('Stock_location')->nullable();
            $table->string('Notification')->nullable();
            $table->date('last_reorder_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_table');
    }
};
