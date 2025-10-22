<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Access_PR\Purchase_Request\PurchaseRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FixPurchaseRequestOwnership extends Command
{
    protected $signature = 'fix:pr-ownership {--check-only} {--user-id=}';
    protected $description = 'Fix Purchase Request ownership data type issues';

    public function handle()
    {
        $this->info('=== FIXING PURCHASE REQUEST OWNERSHIP ===');
        $this->newLine();

        // 1. Check data types
        $this->info('1. ANALYZING DATA TYPES');
        $this->line('---------------------------');
        
        $users = User::all();
        $prs = PurchaseRequest::with('user')->get();
        
        $this->line("Users count: {$users->count()}");
        $this->line("PRs count: {$prs->count()}");
        $this->newLine();

        // 2. Check for type mismatches
        $this->info('2. CHECKING TYPE MISMATCHES');
        $this->line('-----------------------------');
        
        $mismatches = [];
        
        foreach ($prs as $pr) {
            $prUserId = $pr->user_id;
            $actualUser = $pr->user;
            
            if ($actualUser) {
                $actualUserId = $actualUser->id;
                
                // Check strict equality
                $strictMatch = ($prUserId === $actualUserId);
                $looseMatch = ($prUserId == $actualUserId);
                
                if (!$strictMatch && $looseMatch) {
                    $mismatches[] = [
                        'pr_id' => $pr->id,
                        'pr_number' => $pr->pr_number,
                        'pr_user_id' => $prUserId,
                        'pr_user_id_type' => gettype($prUserId),
                        'actual_user_id' => $actualUserId,
                        'actual_user_id_type' => gettype($actualUserId),
                        'user_name' => $actualUser->name
                    ];
                }
            }
        }
        
        if (empty($mismatches)) {
            $this->info('✅ No data type mismatches found!');
        } else {
            $this->warn("⚠️  Found " . count($mismatches) . " data type mismatches:");
            $this->newLine();
            
            foreach ($mismatches as $mismatch) {
                $this->line("PR #{$mismatch['pr_id']} ({$mismatch['pr_number']}):");
                $this->line("  - PR user_id: {$mismatch['pr_user_id']} ({$mismatch['pr_user_id_type']})");
                $this->line("  - User ID: {$mismatch['actual_user_id']} ({$mismatch['actual_user_id_type']})");
                $this->line("  - User: {$mismatch['user_name']}");
                $this->newLine();
            }
        }

        // 3. Test specific user
        if ($this->option('user-id')) {
            $userId = $this->option('user-id');
            $this->info("3. TESTING SPECIFIC USER: {$userId}");
            $this->line('--------------------------------');
            
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found!");
                return 1;
            }
            
            $userPRs = PurchaseRequest::where('user_id', $userId)->get();
            $this->line("User: {$user->name} ({$user->email})");
            $this->line("User ID: {$user->id} (" . gettype($user->id) . ")");
            $this->line("User's PRs: {$userPRs->count()}");
            $this->newLine();
            
            foreach ($userPRs as $pr) {
                $isOwner = ($pr->user_id === $user->id);
                $canView = $pr->canBeViewedByUser($user);
                
                $this->line("PR #{$pr->id}: user_id={$pr->user_id} (" . gettype($pr->user_id) . ")");
                $this->line("  - Is Owner (strict): " . ($isOwner ? 'YES' : 'NO'));
                $this->line("  - Can View: " . ($canView ? 'YES' : 'NO'));
            }
        }

        // 4. Fix if not check-only
        if (!$this->option('check-only') && !empty($mismatches)) {
            $this->newLine();
            $this->info('4. APPLYING FIXES');
            $this->line('------------------');
            
            if ($this->confirm('Do you want to fix the data type mismatches?')) {
                foreach ($mismatches as $mismatch) {
                    DB::table('purchase_requests')
                        ->where('id', $mismatch['pr_id'])
                        ->update(['user_id' => (int) $mismatch['actual_user_id']]);
                    
                    $this->line("✅ Fixed PR #{$mismatch['pr_id']}");
                }
                
                $this->info('All mismatches have been fixed!');
            } else {
                $this->line('Skipped fixing. Run without --check-only to apply fixes.');
            }
        } elseif ($this->option('check-only')) {
            $this->line('Check-only mode. Add --user-id=67 to test specific user or remove --check-only to apply fixes.');
        }

        return 0;
    }
}
