<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('requires_serial')->default(false);
            $table->unsignedSmallInteger('warranty_months')->nullable();
            $table->string('before_image_path')->nullable();
            $table->string('after_image_path')->nullable();
        });

        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('supplier_name');
            $table->string('supplier_contact')->nullable();
            $table->string('invoice_number');
            $table->timestamp('received_at');
            $table->string('delivery_document_path')->nullable();
            $table->string('before_photo_path')->nullable();
            $table->string('after_photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['supplier_name', 'invoice_number']);
        });

        Schema::create('stock_in_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_in_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_cost', 12, 2);
            $table->unsignedSmallInteger('warranty_months')->nullable();
            $table->timestamps();
        });

        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_in_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('sale_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('serial_number')->nullable()->unique();
            $table->unsignedSmallInteger('warranty_months')->nullable();
            $table->string('status')->default('in_stock');
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_units');
        Schema::dropIfExists('stock_in_items');
        Schema::dropIfExists('stock_ins');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['requires_serial', 'warranty_months', 'before_image_path', 'after_image_path']);
        });
    }
};
