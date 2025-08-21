<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $userCount = App\Models\User::count();
    echo "Total users in database: $userCount\n";
    
    if ($userCount > 0) {
        echo "\nUsers:\n";
        $users = App\Models\User::all();
        foreach ($users as $user) {
            echo "- {$user->email} (Level: {$user->level})\n";
        }
    } else {
        echo "No users found in database.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
