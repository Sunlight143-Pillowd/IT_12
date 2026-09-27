<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id('Review_ID');
            $table->foreignId('Product_ID')->constrained('products_table', 'product_ID');
            $table->foreignId('Customer_ID')->constrained('customers_table', 'Customer_ID');
            $table->tinyInteger('Rating')->unsigned();
            $table->text('Review_Text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
