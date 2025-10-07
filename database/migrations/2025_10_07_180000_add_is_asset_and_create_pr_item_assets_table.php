<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add is_asset flag to purchase_request_items
        Schema::table('purchase_request_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_request_items', 'is_asset')) {
                $table->boolean('is_asset')->nullable()->after('purchasing_notes');
            }
        });

        // Create pr_item_assets table for per-unit asset numbers (if not exists)
        if (!Schema::hasTable('pr_item_assets')) {
            Schema::create('pr_item_assets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('purchase_request_id');
                $table->unsignedBigInteger('purchase_request_item_id');
                $table->string('item_description');
                $table->string('base_code');
                $table->string('asset_code'); // e.g., A1-001
                $table->unsignedInteger('sequence_no');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->onDelete('cascade');
                $table->foreign('purchase_request_item_id')->references('id')->on('purchase_request_items')->onDelete('cascade');
                $table->index(['purchase_request_id', 'purchase_request_item_id'], 'pr_item_assets_prid_itmid_idx');
                $table->unique(['purchase_request_id', 'asset_code']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pr_item_assets');
        Schema::table('purchase_request_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_request_items', 'is_asset')) {
                $table->dropColumn('is_asset');
            }
        });
    }
};
