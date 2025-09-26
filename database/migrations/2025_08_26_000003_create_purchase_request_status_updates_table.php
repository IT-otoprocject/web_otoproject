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
        Schema::create('purchase_request_status_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained()->onDelete('cascade');
            $table->enum('update_type', ['VENDOR_SEARCH', 'PRICE_COMPARISON', 'PO_CREATED', 'GOODS_RECEIVED', 'GOODS_RETURNED', 'CLOSED']);
            $table->text('description');
            $table->json('data')->nullable(); // Additional data for each update type
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_request_status_updates');
    }
};
