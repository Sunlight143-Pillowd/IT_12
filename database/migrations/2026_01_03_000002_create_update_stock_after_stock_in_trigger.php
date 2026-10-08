<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            CREATE TRIGGER update_stock_after_stock_in
            AFTER INSERT ON stock_in_table
            FOR EACH ROW
            BEGIN
                UPDATE inventory_table
                SET
                    Current_Stock = Current_Stock + NEW.Quantity,
                    last_reorder_date = NEW.Date_Received,
                    Notification = CASE
                        WHEN (Current_Stock + NEW.Quantity) <= COALESCE(reorder_level, 10)
                        THEN \'Low Stock\'
                        ELSE NULL
                    END
                WHERE Product_ID = NEW.Product_ID;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_stock_after_stock_in');
    }
};
