<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * This runs all seeders in the correct order
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        $this->command->info('');

        // ORDER MATTERS! Run in sequence:

        // 1. Roles & Permissions first (users depend on roles)
        $this->command->info('1️⃣  Creating roles and permissions...');
        $this->call(RolePermissionSeeder::class);
        $this->command->info('');

        // 2. Categories (products depend on categories)
        $this->command->info('2️⃣  Creating categories...');
        $this->call(CategorySeeder::class);
        $this->command->info('');

        // 3. Suppliers (products depend on suppliers)
        $this->command->info('3️⃣  Creating suppliers...');
        $this->call(SupplierSeeder::class);
        $this->command->info('');

        // 4. Users (with roles assigned)
        $this->command->info('4️⃣  Creating demo users...');
        $this->call(UserSeeder::class);
        $this->command->info('');

        // 5. Products last (depends on categories and suppliers)
        $this->command->info('5️⃣  Creating products...');
        $this->call(ProductSeeder::class);
        $this->command->info('');

        $this->command->info('✨ Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('🎉 You can now login with:');
        $this->command->info('   Admin:   admin@stockflow.test / Admin123!');
        $this->command->info('   Manager: manager@stockflow.test / Manager123!');
        $this->command->info('   Staff:   staff@stockflow.test / Staff123!');
        $this->command->info('');
        $this->command->info('🚀 Start your server: php artisan serve');
    }
}
