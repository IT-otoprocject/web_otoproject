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
            // Add the new enum with additional status
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_request_status_updates', function (Blueprint $table) {
            $table->dropColumn('update_type');
        });

        Schema::table('purchase_request_status_updates', function (Blueprint $table) {
            $table->enum('update_type', [
                'VENDOR_SEARCH', 
                'PRICE_COMPARISON', 
                'PO_CREATED', 
                'GOODS_RECEIVED', 
                'GOODS_RETURNED', 
                'CLOSED'
            ])->after('purchase_request_id');
        });
    }
};
