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
        Schema::table('purchase_requests', function (Blueprint $table) {
            // Ubah kolom location dari enum menjadi foreign key
            $table->dropColumn('location');
        });
        
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable()->after('description');
            $table->foreign('location_id')->references('id')->on('master_locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
        });
        
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->enum('location', ['HQ', 'BRANCH', 'OTHER'])->after('description');
        });
    }
};
