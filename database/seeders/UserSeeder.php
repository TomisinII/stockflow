<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates demo users for each role
     */
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@stockflow.test',
            'password' => Hash::make('Admin123!'),
            'email_verified_at' => now(),
            'theme' => 'light',
            'company_name' => 'StockFlow Inc.',
        ]);
        $admin->assignRole('Admin');
        $this->command->info('✅ Admin created: admin@stockflow.test / Admin123!');

        // 2. Create Manager User
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@stockflow.test',
            'password' => Hash::make('Manager123!'),
            'email_verified_at' => now(),
            'theme' => 'light',
            'company_name' => 'StockFlow Inc.',
        ]);
        $manager->assignRole('Manager');
        $this->command->info('✅ Manager created: manager@stockflow.test / Manager123!');

        // 3. Create Staff User
        $staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@stockflow.test',
            'password' => Hash::make('Staff123!'),
            'email_verified_at' => now(),
            'theme' => 'light',
            'company_name' => 'StockFlow Inc.',
        ]);
        $staff->assignRole('Staff');
        $this->command->info('✅ Staff created: staff@stockflow.test / Staff123!');

        // 4. Create additional staff members for testing
        $staff2 = User::create([
            'name' => 'John Doe',
            'email' => 'john@stockflow.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'theme' => 'dark',
            'company_name' => 'Acme Corp',
        ]);
        $staff2->assignRole('Staff');

        $staff3 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@stockflow.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'theme' => 'light',
            'company_name' => 'Tech Solutions Ltd',
        ]);
        $staff3->assignRole('Manager');

        $this->command->info('✅ 5 demo users created successfully!');
        $this->command->info('');
        $this->command->info('📝 Login Credentials:');
        $this->command->info('   Admin:   admin@stockflow.test / Admin123!');
        $this->command->info('   Manager: manager@stockflow.test / Manager123!');
        $this->command->info('   Staff:   staff@stockflow.test / Staff123!');
    }
}
