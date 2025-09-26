<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\User;

echo "Current users data:\n";
foreach(User::all() as $user) {
    echo $user->id . ' - ' . $user->name . ' - ' . $user->level . "\n";
}
