<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // Extend purchase_request_items with payment_method relation
        Schema::table('purchase_request_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_request_items', 'payment_method_id')) {
                $table->unsignedBigInteger('payment_method_id')->nullable()->after('is_asset_hcga');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_request_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_request_items', 'payment_method_id')) {
                $table->dropColumn('payment_method_id');
            }
        });

        Schema::dropIfExists('payment_methods');
    }
};
