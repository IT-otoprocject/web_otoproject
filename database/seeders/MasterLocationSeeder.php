<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterLocation;
use App\Models\User;

class MasterLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user untuk created_by
        $adminUser = User::where('level', 'admin')->first();
        
        if (!$adminUser) {
            $this->command->warn('Admin user not found. Please create admin user first.');
            return;
        }

        $locations = [
            [
                'name' => 'HQ',
                'code' => 'P001',
                'company' => 'PIS',
                'address' => 'Head Quarter Office',
                'phone' => '021-1234567',
                'email' => 'hq@company.com',
                'is_active' => true,
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Branch Jakarta',
                'code' => 'P002', 
                'company' => 'PIS',
                'address' => 'Jakarta Branch Office',
                'phone' => '021-7654321',
                'email' => 'jakarta@company.com',
                'is_active' => true,
                'created_by' => $adminUser->id,
            ],
            [
                'name' => 'Branch Surabaya',
                'code' => 'P003',
                'company' => 'PIS',
                'address' => 'Surabaya Branch Office', 
                'phone' => '031-1234567',
                'email' => 'surabaya@company.com',
                'is_active' => true,
                'created_by' => $adminUser->id,
            ]
        ];

        foreach ($locations as $location) {
            MasterLocation::firstOrCreate(
                ['code' => $location['code']],
                $location
            );
        }

        $this->command->info('Master Location seeder completed successfully.');
    }
}
