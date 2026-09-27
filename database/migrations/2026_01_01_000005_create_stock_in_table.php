<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_in_table', function (Blueprint $table) {
            $table->id('Stockin_ID');
            $table->foreignId('Product_ID')->constrained('products_table', 'product_ID');
            $table->foreignId('Supplier_ID')->constrained('suppliers_table', 'Supplier_ID');
            $table->integer('Quantity');
            $table->date('Date_Received');
            $table->decimal('Cost', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_in_table');
    }
};
