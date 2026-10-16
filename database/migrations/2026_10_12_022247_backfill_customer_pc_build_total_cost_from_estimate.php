<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('pc_builds')
            ->whereNotNull('user_id')
            ->whereNotNull('estimated_cost')
            ->where('total_cost', 0)
            ->update(['total_cost' => DB::raw('estimated_cost')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
