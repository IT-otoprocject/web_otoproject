<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Database Structure ===\n";

try {
    // Check level column structure
    $result = DB::select("SHOW COLUMNS FROM users LIKE 'level'");
    if (!empty($result)) {
        echo "Level column type: " . $result[0]->Type . "\n";
    }
    
    echo "\n=== Checking Users ===\n";
    
    // Check all users and their levels
    $allUsers = User::select('id', 'name', 'email', 'level', 'divisi')->get();
    echo "Total users: " . $allUsers->count() . "\n\n";
    
    foreach ($allUsers as $user) {
        echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Level: {$user->level} | Divisi: {$user->divisi}\n";
    }
    
    echo "\n=== CEO/CFO Users ===\n";
    $ceoUsers = User::where('level', 'ceo')->get();
    $cfoUsers = User::where('level', 'cfo')->get();
    
    echo "CEO users count: " . $ceoUsers->count() . "\n";
    echo "CFO users count: " . $cfoUsers->count() . "\n";
    
    if ($ceoUsers->count() > 0) {
        foreach ($ceoUsers as $user) {
            echo "CEO: {$user->name} ({$user->email})\n";
        }
    }
    
    if ($cfoUsers->count() > 0) {
        foreach ($cfoUsers as $user) {
            echo "CFO: {$user->name} ({$user->email})\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
