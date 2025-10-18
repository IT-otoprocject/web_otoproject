<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('purchase_request_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_request_items', 'is_asset_hcga')) {
                $table->boolean('is_asset_hcga')->nullable()->after('is_asset');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_request_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_request_items', 'is_asset_hcga')) {
                $table->dropColumn('is_asset_hcga');
            }
        });
    }
};
