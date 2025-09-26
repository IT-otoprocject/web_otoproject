<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom divisi
            $table->enum('divisi', [
                'FACTORY',
                'FAT', 
                'HCGA',
                'RETAIL',
                'PDCA',
                'PURCHASING',
                'R&D',
                'SALES',
                'WAREHOUSE',
                'WAREHOUSE_SBY'
            ])->nullable()->after('level');
        });

        // Update existing data - set divisi RETAIL untuk user yang ada SPK/garage
        DB::table('users')
            ->whereIn('level', ['kasir', 'mekanik'])
            ->update(['divisi' => 'RETAIL']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('divisi');
        });
    }
};
