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
        Schema::table('purchase_request_status_updates', function (Blueprint $table) {
            // Drop the existing enum constraint
            $table->dropColumn('update_type');
        });

        Schema::table('purchase_request_status_updates', function (Blueprint $table) {
            // Add the new enum with ITEMS_PROCESSED
            $table->enum('update_type', [
                'ITEMS_PROCESSED',
                'VENDOR_SEARCH', 
                'PRICE_COMPARISON', 
                'PO_CREATED', 
                'GOODS_RECEIVED', 
                'GOODS_RETURNED', 
                'TERSEDIA_DI_GA',
                'CLOSED'
            ])->after('purchase_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_request_status_updates', function (Blueprint $table) {
            // Drop the enum with ITEMS_PROCESSED
            $table->dropColumn('update_type');
        });

        Schema::table('purchase_request_status_updates', function (Blueprint $table) {
            // Restore the previous enum without ITEMS_PROCESSED
            $table->enum('update_type', [
                'VENDOR_SEARCH', 
                'PRICE_COMPARISON', 
                'PO_CREATED', 
                'GOODS_RECEIVED', 
                'GOODS_RETURNED', 
                'TERSEDIA_DI_GA',
                'CLOSED'
            ])->after('purchase_request_id');
        });
    }
};
