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
        // Add additional protection - ensure pr_number and request_date combination is unique
        // This helps prevent race condition issues during high concurrent requests
        Schema::table('purchase_requests', function (Blueprint $table) {
            // First drop the existing unique constraint on pr_number
            $table->dropUnique(['pr_number']);
            
            // Add compound unique index on pr_number and request_date
            // This ensures that even if there's a race condition, we get better error handling
            $table->unique(['pr_number', 'request_date'], 'purchase_requests_pr_number_date_unique');
            
            // Add regular unique constraint back for pr_number (primary protection)
            $table->unique('pr_number', 'purchase_requests_pr_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            // Drop the compound unique index
            $table->dropUnique('purchase_requests_pr_number_date_unique');
            
            // Keep the original pr_number unique constraint
            // (it should already exist from the up method)
        });
    }
};
