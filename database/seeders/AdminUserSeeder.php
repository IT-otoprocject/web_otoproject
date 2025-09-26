<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $adminExists = User::where('email', 'admin@garage.com')->first();
        
        if (!$adminExists) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@garage.com',
                'password' => Hash::make('password123'),
                'level' => 'admin',
                'garage' => null,
                'system_access' => ['dashboard', 'spk_garage', 'pr', 'reports', 'users', 'settings'],
            ]);

            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@garage.com');
            $this->command->info('Password: password123');
        } else {
            $this->command->info('Admin user already exists!');
        }

        // Create sample users with different access levels
        $users = [
            [
                'name' => 'SPK Staff',
                'email' => 'spk@garage.com',
                'password' => Hash::make('password123'),
                'level' => 'kasir',
                'garage' => 'Garage A',
                'system_access' => ['spk_garage', 'dashboard'],
            ],
            [
                'name' => 'PR Staff',
                'email' => 'pr@garage.com',
                'password' => Hash::make('password123'),
                'level' => 'pr_user',
                'garage' => null,
                'system_access' => ['pr', 'dashboard'],
            ],
            [
                'name' => 'Mekanik User',
                'email' => 'mekanik@garage.com',
                'password' => Hash::make('password123'),
                'level' => 'mekanik',
                'garage' => 'Garage A',
                'system_access' => ['spk_garage', 'dashboard'],
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@garage.com',
                'password' => Hash::make('password123'),
                'level' => 'manager',
                'garage' => null,
                'system_access' => ['spk_garage', 'pr', 'dashboard', 'reports'],
            ],
        ];

        foreach ($users as $userData) {
            $existingUser = User::where('email', $userData['email'])->first();
            if (!$existingUser) {
                User::create($userData);
                $this->command->info("User {$userData['name']} created successfully!");
            }
        }
    }
}
