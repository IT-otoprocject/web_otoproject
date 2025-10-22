<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FixUserPermissions extends Command
{
    protected $signature = 'fix:user-permissions {email} {--add-pr} {--make-admin} {--show-current}';
    protected $description = 'Fix user permissions for Purchase Request access';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found!");
            return 1;
        }

        $this->info("User found: {$user->name} ({$user->email})");
        $this->line("Current Level: {$user->level}");
        $this->line("Current Divisi: " . ($user->divisi ?? 'NULL'));
        
        $currentAccess = is_array($user->system_access) ? $user->system_access : [];
        $this->line("Current System Access: " . json_encode($currentAccess));
        $this->newLine();

        if ($this->option('show-current')) {
            $this->info('Current permissions shown above. Use --add-pr or --make-admin to modify.');
            return 0;
        }

        if ($this->option('make-admin')) {
            $user->level = 'admin';
            $user->system_access = [
                'dashboard',
                'user_management', 
                'pr',
                'pr_categories',
                'master_location',
                'spk_management',
                'inventory',
                'reports',
                'settings'
            ];
            $user->save();
            
            $this->info('✅ User has been made admin with full permissions!');
            return 0;
        }

        if ($this->option('add-pr')) {
            if (!in_array('pr', $currentAccess)) {
                $currentAccess[] = 'pr';
                if (!in_array('dashboard', $currentAccess)) {
                    $currentAccess[] = 'dashboard';
                }
                $user->system_access = $currentAccess;
                $user->save();
                
                $this->info('✅ Added PR access to user!');
                $this->line("New System Access: " . json_encode($currentAccess));
            } else {
                $this->info('User already has PR access.');
            }
            return 0;
        }

        $this->info('Available options:');
        $this->line('--show-current    Show current user permissions');
        $this->line('--add-pr         Add Purchase Request access');
        $this->line('--make-admin     Make user admin with full permissions');
        $this->newLine();
        $this->line('Example usage:');
        $this->line("php artisan fix:user-permissions {$email} --add-pr");
        $this->line("php artisan fix:user-permissions {$email} --make-admin");

        return 0;
    }
}
