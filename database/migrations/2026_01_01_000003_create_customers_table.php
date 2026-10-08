<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers_table', function (Blueprint $table) {
            $table->id('Customer_ID');
            $table->string('Customer_Name');
            $table->string('Customer_Contact');
            $table->string('Customer_loyalty_tier')->nullable();
            $table->date('registration_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers_table');
    }
};
