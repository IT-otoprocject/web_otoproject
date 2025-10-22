<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Access_PR\Purchase_Request\PurchaseRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DebugPurchaseRequest extends Command
{
    protected $signature = 'debug:purchase-request {user_email?}';
    protected $description = 'Debug Purchase Request visibility issues';

    public function handle()
    {
        $this->info('=== PURCHASE REQUEST DEBUG TOOL ===');
        $this->newLine();

        // 1. Database Connection Test
        $this->info('1. TESTING DATABASE CONNECTION');
        $this->line('--------------------------------');
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection: SUCCESS');
            $this->line('DB Name: ' . config('database.connections.mysql.database'));
        } catch (\Exception $e) {
            $this->error('❌ Database connection: FAILED');
            $this->error('Error: ' . $e->getMessage());
            return;
        }
        $this->newLine();

        // 2. Check Tables
        $this->info('2. CHECKING TABLE STRUCTURE');
        $this->line('----------------------------');
        $tables = ['users', 'purchase_requests', 'purchase_request_items', 'master_locations', 'pr_categories'];
        foreach ($tables as $table) {
            try {
                DB::table($table)->count();
                $this->info("✅ Table '$table' exists");
            } catch (\Exception $e) {
                $this->error("❌ Table '$table' NOT found");
            }
        }
        $this->newLine();

        // 3. Data Count
        $this->info('3. DATA COUNT VERIFICATION');
        $this->line('---------------------------');
        try {
            $this->line('📊 users: ' . User::count() . ' records');
            $this->line('📊 purchase_requests: ' . PurchaseRequest::count() . ' records');
            $this->line('📊 purchase_request_items: ' . DB::table('purchase_request_items')->count() . ' records');
            $this->line('📊 master_locations: ' . DB::table('master_locations')->count() . ' records');
            $this->line('📊 pr_categories: ' . DB::table('pr_categories')->count() . ' records');
        } catch (\Exception $e) {
            $this->error('Error counting data: ' . $e->getMessage());
        }
        $this->newLine();

        // 4. Recent Purchase Requests
        $this->info('4. RECENT PURCHASE REQUESTS DATA');
        $this->line('---------------------------------');
        try {
            $prs = PurchaseRequest::with('user')->orderBy('created_at', 'desc')->limit(10)->get();
            if ($prs->count() > 0) {
                $this->line('Recent Purchase Requests:');
                foreach ($prs as $pr) {
                    $this->line(sprintf(
                        'ID: %s | PR: %s | Status: %s | User: %s (%s) | Divisi: %s | Level: %s | Created: %s',
                        $pr->id,
                        $pr->pr_number ?? 'NULL',
                        $pr->status,
                        $pr->user->name ?? 'NULL',
                        $pr->user->email ?? 'NULL',
                        $pr->user->divisi ?? 'NULL',
                        $pr->user->level ?? 'NULL',
                        $pr->created_at
                    ));
                }
            } else {
                $this->warn('⚠️  No Purchase Requests found!');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
        $this->newLine();

        // 5. User Check
        $this->info('5. USER AUTHENTICATION CHECK');
        $this->line('-----------------------------');
        $userEmail = $this->argument('user_email');
        
        if ($userEmail) {
            $user = User::where('email', $userEmail)->first();
            if (!$user) {
                $this->error("User with email '$userEmail' not found!");
                return;
            }
            $this->debugUserAccess($user);
        } else {
            $this->line('Sample Users:');
            $users = User::limit(5)->get();
            foreach ($users as $user) {
                $this->line(sprintf(
                    'ID: %s | Name: %s | Email: %s | Divisi: %s | Level: %s | Access: %s',
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->divisi ?? 'NULL',
                    $user->level ?? 'NULL',
                    is_array($user->system_access) ? json_encode($user->system_access) : 'NULL'
                ));
            }
        }
        $this->newLine();

        // 6. Environment Check
        $this->info('6. ENVIRONMENT CONFIGURATION');
        $this->line('-----------------------------');
        $this->line('APP_ENV: ' . config('app.env'));
        $this->line('APP_DEBUG: ' . (config('app.debug') ? 'true' : 'false'));
        $this->line('APP_URL: ' . config('app.url'));
        $this->newLine();

        $this->info('=== DEBUG COMPLETED ===');
        $this->line('To debug specific user access, run:');
        $this->line('php artisan debug:purchase-request user@example.com');
    }

    private function debugUserAccess($user)
    {
        $this->info("Debugging access for user: {$user->name} ({$user->email})");
        $this->newLine();

        // User info
        $this->line("User Details:");
        $this->line("- ID: {$user->id}");
        $this->line("- Name: {$user->name}");
        $this->line("- Email: {$user->email}");
        $this->line("- Level: {$user->level}");
        $this->line("- Divisi: " . ($user->divisi ?? 'NULL'));
        $systemAccess = is_array($user->system_access) ? $user->system_access : [];
        $this->line("- System Access: " . json_encode($systemAccess));
        $this->newLine();

        // System access check
        if (in_array('pr', $systemAccess) || $user->level === 'admin') {
            $this->info('✅ User has PR system access');
        } else {
            $this->error('❌ User does NOT have PR system access');
            $this->line('Available accesses: ' . implode(', ', $systemAccess));
        }
        $this->newLine();

        // PR visibility test
        $this->info('Testing PR Visibility:');
        $prs = PurchaseRequest::with('user')->orderBy('created_at', 'desc')->limit(5)->get();
        
        $visibleCount = 0;
        foreach ($prs as $pr) {
            $canView = $pr->canBeViewedByUser($user);
            if ($canView) $visibleCount++;
            
            $status = $canView ? '✅ CAN VIEW' : '❌ CANNOT VIEW';
            $this->line("PR #{$pr->id} ({$pr->pr_number}) - Owner: {$pr->user->name} - {$status}");
        }
        
        $this->newLine();
        $this->line("Summary:");
        $this->line("- Total PRs in system: " . PurchaseRequest::count());
        $this->line("- PRs visible to user: {$visibleCount}");
        $this->line("- User's own PRs: " . PurchaseRequest::where('user_id', $user->id)->count());
    }
}
