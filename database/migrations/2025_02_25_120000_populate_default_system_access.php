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
        // Update existing users with default system_access based on their level
        $users = DB::table('users')->get();
        
        foreach ($users as $user) {
            $systemAccess = $this->getDefaultAccessByLevel($user->level);
            
            DB::table('users')
                ->where('id', $user->id)
                ->update(['system_access' => json_encode($systemAccess)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set all system_access to null
        DB::table('users')->update(['system_access' => null]);
    }

    /**
     * Get default access based on user level
     */
    private function getDefaultAccessByLevel($level)
    {
        return match($level) {
            'admin' => ['dashboard', 'spk_garage', 'pr', 'reports', 'pr_reports', 'spk_reports', 'inventory_reports', 'users', 'settings'],
            'manager' => ['spk_garage', 'pr', 'dashboard', 'reports', 'pr_reports', 'spk_reports'],
            'spv' => ['spk_garage', 'pr', 'dashboard', 'reports', 'pr_reports', 'spk_reports'],
            'staff' => ['dashboard'],
            'headstore' => ['spk_garage', 'dashboard', 'reports', 'spk_reports'],
            'kasir' => ['spk_garage', 'dashboard'],
            'sales' => ['spk_garage', 'dashboard'],
            'mekanik' => ['spk_garage', 'dashboard'],
            'pr_user' => ['pr', 'dashboard', 'pr_reports'],
            'purchasing' => ['pr', 'dashboard', 'reports', 'pr_reports'],
            'ga' => ['pr', 'dashboard', 'reports', 'pr_reports'],
            default => ['dashboard']
        };
    }
};
