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
        Schema::table('pc_builds', function (Blueprint $table) {
            $table->decimal('estimated_cost', 12, 2)->nullable()->after('total_cost');
        });

        $productPricesByName = DB::table('products')
            ->get(['name', 'price'])
            ->groupBy('name')
            ->map(fn ($products): ?string => $products->count() === 1 ? $products->first()->price : null);

        DB::table('pc_builds')
            ->whereNotNull('user_id')
            ->whereNull('estimated_cost')
            ->chunkById(100, function ($builds) use ($productPricesByName): void {
                $builds->each(function (object $build) use ($productPricesByName): void {
                    $componentNames = collect(preg_split('/\R/', (string) $build->notes) ?: [])
                        ->map(function (string $line): ?string {
                            if (preg_match('/^(?:CPU|Motherboard|GPU|RAM|Storage|PSU|PC Case|CPU Cooler):\s*(.+)$/i', trim($line), $matches) !== 1) {
                                return null;
                            }

                            return trim($matches[1]);
                        })
                        ->filter()
                        ->values();

                    if ($componentNames->isEmpty()) {
                        return;
                    }

                    $prices = $componentNames->map(
                        fn (string $name): ?string => $productPricesByName->get($name)
                    );

                    if ($prices->contains(null)) {
                        return;
                    }

                    $estimatedCostInCents = $prices->sum(
                        fn (string $price): int => (int) round((float) $price * 100)
                    );

                    DB::table('pc_builds')
                        ->where('id', $build->id)
                        ->update(['estimated_cost' => $estimatedCostInCents / 100]);
                });
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pc_builds', function (Blueprint $table) {
            $table->dropColumn('estimated_cost');
        });
    }
};
