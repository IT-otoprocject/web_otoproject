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
        // Tambah kolom divisi terlebih dahulu
        Schema::table('users', function (Blueprint $table) {
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
            ])->after('level')->nullable();
        });

        // Tambah kolom sementara untuk level baru
        Schema::table('users', function (Blueprint $table) {
            $table->enum('new_level', [
                'admin',
                'manager', 
                'spv',
                'staff',
                'headstore',
                'kasir',
                'sales', 
                'mekanik'
            ])->after('divisi')->default('staff');
        });

        // Mapping level lama ke level baru dan set divisi
        DB::statement("UPDATE users SET new_level = 'admin', divisi = 'HCGA' WHERE level = 'admin'");
        DB::statement("UPDATE users SET new_level = 'kasir', divisi = 'RETAIL' WHERE level = 'kasir'");
        DB::statement("UPDATE users SET new_level = 'mekanik', divisi = 'RETAIL' WHERE level = 'mekanik'");
        DB::statement("UPDATE users SET new_level = 'staff', divisi = 'HCGA' WHERE level = 'ga'");
        DB::statement("UPDATE users SET new_level = 'staff', divisi = 'PURCHASING' WHERE level = 'purchasing'");
        DB::statement("UPDATE users SET new_level = 'staff', divisi = 'PURCHASING' WHERE level = 'pr_user'");

        // Hapus kolom level lama
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('level');
        });

        // Rename kolom new_level menjadi level
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('new_level', 'level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tambah kolom level lama
        Schema::table('users', function (Blueprint $table) {
            $table->enum('old_level', [
                'admin',
                'kasir',
                'mekanik', 
                'ga',
                'purchasing',
                'pr_user'
            ])->after('level')->default('kasir');
        });

        // Mapping level baru ke level lama
        DB::statement("UPDATE users SET old_level = 'admin' WHERE level = 'admin'");
        DB::statement("UPDATE users SET old_level = 'kasir' WHERE level = 'kasir'");
        DB::statement("UPDATE users SET old_level = 'mekanik' WHERE level = 'mekanik'");
        DB::statement("UPDATE users SET old_level = 'ga' WHERE level = 'staff' AND divisi = 'HCGA'");
        DB::statement("UPDATE users SET old_level = 'purchasing' WHERE level = 'staff' AND divisi = 'PURCHASING'");

        // Hapus kolom level dan divisi
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['level', 'divisi']);
        });

        // Rename kolom old_level menjadi level
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('old_level', 'level');
        });
    }
};
