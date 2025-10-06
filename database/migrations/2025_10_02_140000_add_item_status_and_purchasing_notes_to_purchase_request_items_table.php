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
        Schema::table('purchase_request_items', function (Blueprint $table) {
            $table->enum('item_status', [
                'PENDING',
                'VENDOR_SEARCH', 
                'PRICE_COMPARISON',
                'PO_CREATED',
                'GOODS_RECEIVED',
                'GOODS_RETURNED',
                'TERSEDIA_DI_GA',
                'CLOSED'
            ])->default('PENDING')->after('notes');
            $table->text('purchasing_notes')->nullable()->after('item_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_request_items', function (Blueprint $table) {
            $table->dropColumn(['item_status', 'purchasing_notes']);
        });
    }
};
