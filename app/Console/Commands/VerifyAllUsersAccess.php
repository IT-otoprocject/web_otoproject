<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Access_PR\Purchase_Request\PurchaseRequest;
use App\Models\User;

class VerifyAllUsersAccess extends Command
{
    protected $signature = 'verify:all-users-pr-access';
    protected $description = 'Verify all users can access their own Purchase Requests';

    public function handle()
    {
        $this->info('=== VERIFYING ALL USERS PR ACCESS ===');
        $this->newLine();

        // Get all users who have created PRs
        $usersWithPRs = User::whereHas('purchaseRequests')->with('purchaseRequests')->get();
        
        $this->line("Found {$usersWithPRs->count()} users with Purchase Requests");
        $this->newLine();

        $totalPRs = 0;
        $accessiblePRs = 0;
        $issuesFound = [];

        foreach ($usersWithPRs as $user) {
            $userPRs = $user->purchaseRequests;
            $userAccessibleCount = 0;
            
            foreach ($userPRs as $pr) {
                $totalPRs++;
                $canView = $pr->canBeViewedByUser($user);
                
                if ($canView) {
                    $userAccessibleCount++;
                    $accessiblePRs++;
                } else {
                    // Log issue
                    $issuesFound[] = [
                        'user' => $user->name,
                        'email' => $user->email,
                        'user_id' => $user->id,
                        'pr_id' => $pr->id,
                        'pr_number' => $pr->pr_number,
                        'pr_user_id' => $pr->user_id,
                        'pr_user_id_type' => gettype($pr->user_id),
                        'user_id_type' => gettype($user->id)
                    ];
                }
            }
            
            $status = ($userAccessibleCount === $userPRs->count()) ? '✅' : '❌';
            $this->line("{$status} {$user->name} ({$user->email}): {$userAccessibleCount}/{$userPRs->count()} PRs accessible");
        }

        $this->newLine();
        $this->info("=== SUMMARY ===");
        $this->line("Total PRs: {$totalPRs}");
        $this->line("Accessible PRs: {$accessiblePRs}");
        $this->line("Issues: " . count($issuesFound));
        
        if (!empty($issuesFound)) {
            $this->newLine();
            $this->warn("ISSUES FOUND:");
            foreach ($issuesFound as $issue) {
                $this->line("❌ {$issue['user']} cannot view PR #{$issue['pr_id']} ({$issue['pr_number']})");
                $this->line("   User ID: {$issue['user_id']} ({$issue['user_id_type']})");
                $this->line("   PR user_id: {$issue['pr_user_id']} ({$issue['pr_user_id_type']})");
                $this->newLine();
            }
            
            $this->line("Run 'php artisan fix:pr-ownership' to fix data type issues.");
        } else {
            $this->info("🎉 All users can access their Purchase Requests correctly!");
        }

        return 0;
    }
}
