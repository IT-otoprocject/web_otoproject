<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Access_PR\Purchase_Request\PurchaseRequest;
use App\Models\User;

class DeepDebugPurchaseRequest extends Command
{
    protected $signature = 'debug:pr-deep {user_email} {pr_id?}';
    protected $description = 'Deep debug Purchase Request authorization logic';

    public function handle()
    {
        $userEmail = $this->argument('user_email');
        $prId = $this->argument('pr_id');
        
        $user = User::where('email', $userEmail)->first();
        if (!$user) {
            $this->error("User not found: $userEmail");
            return 1;
        }

        $this->info("=== DEEP DEBUG AUTHORIZATION ===");
        $this->info("User: {$user->name} ({$user->email})");
        $this->line("ID: {$user->id}, Level: {$user->level}, Divisi: {$user->divisi}");
        $this->newLine();

        // Get PR to test
        if ($prId) {
            $prs = PurchaseRequest::with(['user', 'items', 'location', 'category'])->where('id', $prId)->get();
        } else {
            $prs = PurchaseRequest::with(['user', 'items', 'location', 'category'])->where('user_id', $user->id)->get();
        }

        if ($prs->isEmpty()) {
            $this->warn("No PRs found for debugging");
            return 0;
        }

        foreach ($prs as $pr) {
            $this->info("=== TESTING PR #{$pr->id} ({$pr->pr_number}) ===");
            $this->line("Owner: {$pr->user->name} (ID: {$pr->user_id})");
            $this->line("Status: {$pr->status}");
            $this->line("Approval Flow: " . json_encode($pr->approval_flow));
            $this->newLine();

            // Test each condition in canBeViewedByUser
            $this->info("Testing Authorization Conditions:");
            
            // 1. Owner check
            $isOwner = ($pr->user_id === $user->id);
            $this->line("1. Is Owner: " . ($isOwner ? "✅ YES" : "❌ NO") . " ({$pr->user_id} === {$user->id})");
            
            // 2. Admin/Purchasing check
            $isAdmin = in_array($user->level, ['admin']);
            $isPurchasing = ($user->divisi === 'PURCHASING' && in_array($user->level, ['manager', 'spv', 'staff']));
            $this->line("2. Is Admin: " . ($isAdmin ? "✅ YES" : "❌ NO"));
            $this->line("3. Is Purchasing: " . ($isPurchasing ? "✅ YES" : "❌ NO"));
            
            // 3. Can approve check
            $canApprove = $pr->canBeApprovedByUser($user);
            $this->line("4. Can Approve: " . ($canApprove ? "✅ YES" : "❌ NO"));
            
            // 4. Has approved check
            $hasApproved = $pr->hasBeenApprovedByUser($user);
            $this->line("5. Has Approved: " . ($hasApproved ? "✅ YES" : "❌ NO"));
            
            // 5. HCGA check
            $isHCGA = ($user->divisi === 'HCGA' && in_array($user->level, ['manager', 'spv', 'staff']));
            $hasGAFlow = in_array('ga', $pr->approval_flow ?? []);
            $this->line("6. Is HCGA + GA Flow: " . (($isHCGA && $hasGAFlow) ? "✅ YES" : "❌ NO"));
            
            // 6. Dept Head check
            $isDeptHead = ($user->level === 'manager');
            $hasDeptHeadFlow = in_array('dept_head', $pr->approval_flow ?? []);
            $sameDivisi = ($user->divisi === $pr->user->divisi);
            $this->line("7. Is Dept Head + Same Divisi + Flow: " . (($isDeptHead && $hasDeptHeadFlow && $sameDivisi) ? "✅ YES" : "❌ NO"));
            
            // 7. FAT check
            $isFAT = ($user->divisi === 'FAT' && in_array($user->level, ['manager', 'spv']));
            $hasFinanceFlow = in_array('finance_dept', $pr->approval_flow ?? []);
            $this->line("8. Is FAT + Finance Flow: " . (($isFAT && $hasFinanceFlow) ? "✅ YES" : "❌ NO"));
            
            // Final result
            $canView = $pr->canBeViewedByUser($user);
            $this->newLine();
            $this->line("FINAL RESULT: " . ($canView ? "✅ CAN VIEW" : "❌ CANNOT VIEW"));
            
            // Show the logic breakdown
            $shouldView = $isOwner || $isAdmin || $isPurchasing || $canApprove || $hasApproved || 
                         ($isHCGA && $hasGAFlow) || ($isDeptHead && $hasDeptHeadFlow && $sameDivisi) || 
                         ($isFAT && $hasFinanceFlow);
            
            $this->line("Expected Result: " . ($shouldView ? "✅ SHOULD VIEW" : "❌ SHOULD NOT VIEW"));
            
            if ($canView !== $shouldView) {
                $this->error("⚠️  MISMATCH! Model result differs from expected logic!");
            }
            
            $this->newLine();
            $this->line("=== DETAILED CHECKS ===");
            
            // Check approval flow details
            if ($pr->approval_flow) {
                $this->line("Approval Flow Details:");
                foreach ($pr->approval_flow as $level) {
                    $this->line("  - $level");
                }
            }
            
            // Check approvals
            if ($pr->approvals) {
                $this->line("Current Approvals:");
                foreach ($pr->approvals as $level => $approval) {
                    $approved = $approval['approved'] ?? false;
                    $approver = $approval['approved_by'] ?? 'N/A';
                    $this->line("  - $level: " . ($approved ? 'APPROVED' : 'PENDING') . " by $approver");
                }
            }
            
            $this->line(str_repeat("=", 60));
        }

        return 0;
    }
}
