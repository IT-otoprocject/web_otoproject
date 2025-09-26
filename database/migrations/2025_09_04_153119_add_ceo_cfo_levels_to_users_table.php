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
        // Update existing users with CEO or CFO in name to appropriate level
        DB::table('users')
            ->where('name', 'LIKE', '%CEO%')
            ->orWhere('name', 'LIKE', '%Chief Executive%')
            ->update(['level' => 'ceo']);
            
        DB::table('users')
            ->where('name', 'LIKE', '%CFO%')
            ->orWhere('name', 'LIKE', '%Chief Financial%')
            ->update(['level' => 'cfo']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert CEO and CFO levels back to admin
        DB::table('users')
            ->whereIn('level', ['ceo', 'cfo'])
            ->update(['level' => 'admin']);
    }
};
