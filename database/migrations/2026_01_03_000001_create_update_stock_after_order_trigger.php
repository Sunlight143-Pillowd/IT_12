<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            CREATE TRIGGER update_stock_after_order
            AFTER INSERT ON orders_items
            FOR EACH ROW
            BEGIN
                UPDATE inventory_table
                SET
                    Current_Stock = Current_Stock - NEW.quantity,
                    Notification = CASE
                        WHEN (Current_Stock - NEW.quantity) <= COALESCE(reorder_level, 10)
                        THEN \'Low Stock\'
                        ELSE NULL
                    END
                WHERE Product_ID = NEW.product_ID;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_stock_after_order');
    }
};
