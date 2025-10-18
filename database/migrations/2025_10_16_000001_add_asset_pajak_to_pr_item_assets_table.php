<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pr_item_assets', function (Blueprint $table) {
            if (!Schema::hasColumn('pr_item_assets', 'asset_pajak')) {
                $table->boolean('asset_pajak')->nullable()->after('asset_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pr_item_assets', function (Blueprint $table) {
            if (Schema::hasColumn('pr_item_assets', 'asset_pajak')) {
                $table->dropColumn('asset_pajak');
            }
        });
    }
};
