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
        // Modify the level column to include CEO and CFO values
        DB::statement("ALTER TABLE users MODIFY COLUMN level ENUM('admin', 'manager', 'spv', 'staff', 'kasir', 'mekanik', 'ceo', 'cfo') DEFAULT 'kasir'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert level column to original values (remove CEO and CFO)
        DB::statement("ALTER TABLE users MODIFY COLUMN level ENUM('admin', 'manager', 'spv', 'staff', 'kasir', 'mekanik') DEFAULT 'kasir'");
    }
};
