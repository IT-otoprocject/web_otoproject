<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class FixLiveServer extends Command
{
    protected $signature = 'fix:live-server';
    protected $description = 'Fix common live server issues for Purchase Request system';

    public function handle()
    {
        $this->info('=== FIXING LIVE SERVER ISSUES ===');
        $this->newLine();

        // 1. Clear all caches
        $this->info('1. Clearing Laravel caches...');
        $this->line('--------------------------------');
        
        try {
            Artisan::call('config:clear');
            $this->line('✅ Config cache cleared');
            
            Artisan::call('cache:clear');
            $this->line('✅ Application cache cleared');
            
            Artisan::call('view:clear');
            $this->line('✅ View cache cleared');
            
            Artisan::call('route:clear');
            $this->line('✅ Route cache cleared');
            
            Artisan::call('optimize:clear');
            $this->line('✅ All optimization caches cleared');
        } catch (\Exception $e) {
            $this->error('Error clearing caches: ' . $e->getMessage());
        }
        $this->newLine();

        // 2. Check environment
        $this->info('2. Environment Check...');
        $this->line('------------------------');
        $this->line('APP_ENV: ' . config('app.env'));
        $this->line('APP_DEBUG: ' . (config('app.debug') ? 'true' : 'false'));
        $this->line('DB_CONNECTION: ' . config('database.default'));
        $this->newLine();

        // 3. Check database connection
        $this->info('3. Testing Database Connection...');
        $this->line('----------------------------------');
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection successful');
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
            return 1;
        }
        $this->newLine();

        // 4. Check migrations
        $this->info('4. Checking Database Migrations...');
        $this->line('-----------------------------------');
        try {
            Artisan::call('migrate:status');
            $this->line(Artisan::output());
        } catch (\Exception $e) {
            $this->error('Error checking migrations: ' . $e->getMessage());
        }
        $this->newLine();

        // 5. Rebuild caches for production (if not local)
        if (!app()->isLocal()) {
            $this->info('5. Rebuilding Production Caches...');
            $this->line('-----------------------------------');
            try {
                Artisan::call('config:cache');
                $this->line('✅ Config cached');
                
                Artisan::call('route:cache');
                $this->line('✅ Routes cached');
                
                Artisan::call('view:cache');
                $this->line('✅ Views cached');
            } catch (\Exception $e) {
                $this->error('Error rebuilding caches: ' . $e->getMessage());
            }
        } else {
            $this->info('5. Skipping production cache rebuild (local environment)');
        }
        $this->newLine();

        // 6. Check storage link
        $this->info('6. Checking Storage Link...');
        $this->line('----------------------------');
        try {
            if (!file_exists(public_path('storage'))) {
                Artisan::call('storage:link');
                $this->line('✅ Storage link created');
            } else {
                $this->line('✅ Storage link already exists');
            }
        } catch (\Exception $e) {
            $this->error('Error with storage link: ' . $e->getMessage());
        }
        $this->newLine();

        // 7. Check file permissions
        $this->info('7. Checking File Permissions...');
        $this->line('--------------------------------');
        $paths = [
            'storage/logs' => storage_path('logs'),
            'storage/app' => storage_path('app'),
            'storage/framework/cache' => storage_path('framework/cache'),
            'bootstrap/cache' => base_path('bootstrap/cache')
        ];

        foreach ($paths as $name => $path) {
            if (is_dir($path)) {
                $writable = is_writable($path);
                $perms = substr(sprintf('%o', fileperms($path)), -4);
                $status = $writable ? '✅' : '❌';
                $this->line("{$status} {$name}: {$perms} " . ($writable ? 'Writable' : 'Not Writable'));
            } else {
                $this->warn("⚠️  {$name}: Directory not found");
            }
        }
        $this->newLine();

        $this->info('=== LIVE SERVER FIX COMPLETED ===');
        $this->line('Next steps:');
        $this->line('1. Test your Purchase Request functionality');
        $this->line('2. If still having issues, run: php artisan debug:purchase-request your@email.com');
        $this->line('3. Check user permissions in database if needed');
        
        return 0;
    }
}
