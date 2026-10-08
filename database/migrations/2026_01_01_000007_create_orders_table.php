<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders_table', function (Blueprint $table) {
            $table->id('Order_Id');
            $table->foreignId('Customer_ID')->constrained('customers_table', 'Customer_ID');
            $table->foreignId('Employee_ID')->constrained('employees_table', 'employee_ID');
            $table->timestamp('Order_Date')->useCurrent();
            $table->decimal('Total_Amount', 10, 2);
            $table->string('Payment_Method')->nullable();
            $table->string('Status')->default('pending');

            // No shipping/address table in the ERD — plain column, add
            // ->constrained() later if you create that table.
            $table->unsignedBigInteger('shipping_address_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders_table');
    }
};
