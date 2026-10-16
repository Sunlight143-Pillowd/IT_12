<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->string('lot_code')->nullable()->index();
            $table->date('manufactured_at')->nullable();
            $table->date('warranty_ends_at')->nullable()->index();
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->string('sku')->nullable()->unique();
        });

        foreach (DB::table('products')->whereNull('sku')->orderBy('id')->cursor() as $product) {
            DB::table('products')->where('id', $product->id)->update(['sku' => 'CB-'.str_pad((string) $product->id, 6, '0', STR_PAD_LEFT)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropColumn(['lot_code', 'manufactured_at', 'warranty_ends_at']);
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->dropUnique(['sku']);
            $table->dropColumn('sku');
        });
    }
};
