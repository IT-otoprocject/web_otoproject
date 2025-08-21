<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('secret'),
            'level' => 'admin',
        ]);

        // Create kasir user
        User::create([
            'name' => 'Kasir',
            'email' => 'kasir@gmail.com',
            'password' => bcrypt('secret'),
            'level' => 'kasir',
        ]);

        // Create mekanik user
        User::create([
            'name' => 'Mekanik',
            'email' => 'mekanik@gmail.com',
            'password' => bcrypt('secret'),
            'level' => 'mekanik',
        ]);
    }
}
