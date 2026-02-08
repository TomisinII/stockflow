<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserSetting;
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
            'phone' => '+234 801 234 5678',
            'email_verified_at' => now(),
            'theme' => 'light',
        ]);
        $admin->assignRole('Admin');

        // Create admin settings
        UserSetting::create([
            'user_id' => $admin->id,
            'company_name' => 'StockFlow Demo Company',
            'company_email' => 'admin@stockflow.com',
            'company_phone' => '+234 801 234 5678',
            'company_website' => 'https://stockflow.com',
            'company_address' => '123 Business Avenue, Lagos, Nigeria',
            'theme' => 'light',
            'language' => 'en',
            'date_format' => 'DD/MM/YYYY',
            'currency' => 'NGN',
            'email_low_stock_alerts' => true,
            'email_order_received' => true,
            'email_daily_summary' => false,
            'push_low_stock_alerts' => true,
            'push_order_updates' => true,
            'low_stock_threshold' => '20',
            'two_factor_enabled' => false,
            'session_timeout' => '30',
            'password_expiry' => '90',
        ]);

        $this->command->info('✅ Admin created: admin@stockflow.test / Admin123!');

        // 2. Create Manager User
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@stockflow.test',
            'password' => Hash::make('Manager123!'),
            'phone' => '+234 802 345 6789',
            'email_verified_at' => now(),
            'theme' => 'light',
        ]);
        $manager->assignRole('Manager');

        // Create manager settings
        UserSetting::create([
            'user_id' => $manager->id,
            'company_name' => 'StockFlow Demo Company',
            'company_email' => 'manager@stockflow.com',
            'company_phone' => '+234 802 345 6789',
            'theme' => 'light',
            'language' => 'en',
            'date_format' => 'DD/MM/YYYY',
            'currency' => 'NGN',
            'email_low_stock_alerts' => true,
            'email_order_received' => true,
            'email_daily_summary' => true,
            'push_low_stock_alerts' => true,
            'push_order_updates' => true,
            'low_stock_threshold' => '20',
            'two_factor_enabled' => false,
            'session_timeout' => '60',
            'password_expiry' => '90',
        ]);

        $this->command->info('✅ Manager created: manager@stockflow.test / Manager123!');

        // 3. Create Staff User
        $staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@stockflow.test',
            'password' => Hash::make('Staff123!'),
            'phone' => '+234 803 456 7890',
            'email_verified_at' => now(),
            'theme' => 'light',
        ]);
        $staff->assignRole('Staff');

        // Create staff settings
        UserSetting::create([
            'user_id' => $staff->id,
            'company_name' => 'StockFlow Demo Company',
            'theme' => 'light',
            'language' => 'en',
            'date_format' => 'DD/MM/YYYY',
            'currency' => 'NGN',
            'email_low_stock_alerts' => true,
            'email_order_received' => false,
            'email_daily_summary' => false,
            'push_low_stock_alerts' => true,
            'push_order_updates' => false,
            'low_stock_threshold' => '30',
            'two_factor_enabled' => false,
            'session_timeout' => '30',
            'password_expiry' => '90',
        ]);

        $this->command->info('✅ Staff created: staff@stockflow.test / Staff123!');

        // 4. Create additional staff members for testing
        $staff2 = User::create([
            'name' => 'John Doe',
            'email' => 'john@stockflow.test',
            'password' => Hash::make('password'),
            'phone' => '+234 803 456 7890',
            'email_verified_at' => now(),
            'theme' => 'dark',
        ]);
        $staff2->assignRole('Staff');

        UserSetting::create([
            'user_id' => $staff2->id,
            'company_name' => 'Acme Corp',
            'company_email' => 'john@acmecorp.com',
            'company_phone' => '+234 803 456 7890',
            'theme' => 'dark',
            'language' => 'en',
            'date_format' => 'MM/DD/YYYY',
            'currency' => 'NGN',
            'email_low_stock_alerts' => false,
            'email_order_received' => true,
            'email_daily_summary' => false,
            'push_low_stock_alerts' => true,
            'push_order_updates' => true,
            'low_stock_threshold' => '10',
            'two_factor_enabled' => false,
            'session_timeout' => '15',
            'password_expiry' => '60',
        ]);

        $staff3 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@stockflow.test',
            'password' => Hash::make('password'),
            'phone' => '+234 804 567 8901',
            'email_verified_at' => now(),
            'theme' => 'system',
        ]);
        $staff3->assignRole('Manager');

        UserSetting::create([
            'user_id' => $staff3->id,
            'company_name' => 'Tech Solutions Ltd',
            'company_email' => 'jane@techsolutions.com',
            'company_phone' => '+234 804 567 8901',
            'company_website' => 'https://techsolutions.com',
            'theme' => 'system',
            'language' => 'en',
            'date_format' => 'YYYY-MM-DD',
            'currency' => 'USD',
            'email_low_stock_alerts' => true,
            'email_order_received' => true,
            'email_daily_summary' => true,
            'push_low_stock_alerts' => true,
            'push_order_updates' => true,
            'low_stock_threshold' => '50',
            'two_factor_enabled' => true,
            'session_timeout' => '120',
            'password_expiry' => 'never',
        ]);

        $this->command->info('✅ 5 demo users created successfully with settings!');
        $this->command->info('');
        $this->command->info('📝 Login Credentials:');
        $this->command->info('   Admin:   admin@stockflow.test / Admin123!');
        $this->command->info('   Manager: manager@stockflow.test / Manager123!');
        $this->command->info('   Staff:   staff@stockflow.test / Staff123!');
        $this->command->info('   John:    john@stockflow.test / password');
        $this->command->info('   Jane:    jane@stockflow.test / password');
    }
}
