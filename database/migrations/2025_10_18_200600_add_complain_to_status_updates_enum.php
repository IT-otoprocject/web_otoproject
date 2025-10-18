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
        // Extend the enum to include COMPLAIN and other used types
        DB::statement(
            "ALTER TABLE purchase_request_status_updates MODIFY COLUMN update_type 
            ENUM('ITEMS_PROCESSED', 'VENDOR_SEARCH', 'PRICE_COMPARISON', 'PO_CREATED', 'GOODS_RECEIVED', 'GOODS_RETURNED', 'TERSEDIA_DI_GA', 'COMPLAIN', 'ASSET_NUMBER_ASSIGNED', 'CLOSED') 
            NOT NULL"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous enum without COMPLAIN and ASSET_NUMBER_ASSIGNED
        DB::statement(
            "ALTER TABLE purchase_request_status_updates MODIFY COLUMN update_type 
            ENUM('ITEMS_PROCESSED', 'VENDOR_SEARCH', 'PRICE_COMPARISON', 'PO_CREATED', 'GOODS_RECEIVED', 'GOODS_RETURNED', 'TERSEDIA_DI_GA', 'CLOSED') 
            NOT NULL"
        );
    }
};
