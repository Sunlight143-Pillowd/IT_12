<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products_table', function (Blueprint $table) {
            $table->id('product_ID');

            // No Product_Categories table in the ERD — plain column, add
            // ->constrained() later if you create that table.
            $table->unsignedBigInteger('product_category_ID')->nullable();

            $table->string('product_name');
            $table->decimal('original_price', 10, 2);
            $table->decimal('Selling_price_units', 10, 2);
            $table->decimal('Selling_price_bg', 10, 2);
            $table->timestamp('last_updated')->nullable();
            $table->string('sku')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products_table');
    }
};
