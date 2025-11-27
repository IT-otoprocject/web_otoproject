<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE purchase_request_status_updates MODIFY COLUMN update_type ENUM('VENDOR_SEARCH', 'PRICE_COMPARISON', 'PO_CREATED', 'GOODS_RECEIVED', 'GOODS_RETURNED', 'CLOSED', 'ITEMS_PROCESSED', 'COMPLAIN', 'TERSEDIA_DI_GA', 'CFO_QUANTITY_ADJUSTMENT') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE purchase_request_status_updates MODIFY COLUMN update_type ENUM('VENDOR_SEARCH', 'PRICE_COMPARISON', 'PO_CREATED', 'GOODS_RECEIVED', 'GOODS_RETURNED', 'CLOSED', 'ITEMS_PROCESSED', 'COMPLAIN', 'TERSEDIA_DI_GA') NOT NULL");
    }
};
