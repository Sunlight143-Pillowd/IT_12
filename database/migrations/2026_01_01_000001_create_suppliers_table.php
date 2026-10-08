<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers_table', function (Blueprint $table) {
            $table->id('Supplier_ID');
            $table->string('Supplier_Name');
            $table->string('Supplier_Contact');
            $table->string('Supplier_Address');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers_table');
    }
};
