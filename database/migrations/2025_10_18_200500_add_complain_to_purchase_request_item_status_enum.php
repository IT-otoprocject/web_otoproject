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
        // Add COMPLAIN to the ENUM list for item_status
        DB::statement(
            "ALTER TABLE purchase_request_items MODIFY COLUMN item_status 
            ENUM('PENDING', 'VENDOR_SEARCH', 'PRICE_COMPARISON', 'PO_CREATED', 'GOODS_RECEIVED', 'GOODS_RETURNED', 'COMPLAIN', 'TERSEDIA_DI_GA', 'REJECTED', 'CLOSED') 
            DEFAULT 'PENDING'"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the previous set without COMPLAIN
        DB::statement(
            "ALTER TABLE purchase_request_items MODIFY COLUMN item_status 
            ENUM('PENDING', 'VENDOR_SEARCH', 'PRICE_COMPARISON', 'PO_CREATED', 'GOODS_RECEIVED', 'GOODS_RETURNED', 'TERSEDIA_DI_GA', 'REJECTED', 'CLOSED') 
            DEFAULT 'PENDING'"
        );
    }
};
