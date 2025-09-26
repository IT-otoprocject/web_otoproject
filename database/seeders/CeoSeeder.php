<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CeoSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create CEO user
        User::updateOrCreate(
            ['email' => 'ceo@company.com'],
            [
                'name' => 'CEO Company',
                'email' => 'ceo@company.com',
                'password' => Hash::make('password123'),
                'level' => 'ceo',
                'divisi' => null, // CEO tidak terikat divisi tertentu
                'email_verified_at' => now()
            ]
        );

        // Create CFO user
        User::updateOrCreate(
            ['email' => 'cfo@company.com'],
            [
                'name' => 'CFO Company',
                'email' => 'cfo@company.com', 
                'password' => Hash::make('password123'),
                'level' => 'cfo',
                'divisi' => 'FAT', // CFO biasanya dari Finance divisi
                'email_verified_at' => now()
            ]
        );
    }
}
