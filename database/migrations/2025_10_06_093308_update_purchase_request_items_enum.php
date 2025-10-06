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
        DB::statement("ALTER TABLE purchase_request_items MODIFY COLUMN item_status ENUM('PENDING', 'VENDOR_SEARCH', 'PRICE_COMPARISON', 'PO_CREATED', 'GOODS_RECEIVED', 'GOODS_RETURNED', 'TERSEDIA_DI_GA', 'REJECTED', 'CLOSED') DEFAULT 'PENDING'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE purchase_request_items MODIFY COLUMN item_status ENUM('PENDING', 'VENDOR_SEARCH', 'PRICE_COMPARISON', 'PO_CREATED', 'GOODS_RECEIVED', 'GOODS_RETURNED', 'TERSEDIA_DI_GA', 'CLOSED') DEFAULT 'PENDING'");
    }
};
