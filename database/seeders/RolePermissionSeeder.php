<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This creates all roles and permissions for the system
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Product permissions
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',

            // Category permissions
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',

            // Supplier permissions
            'view_suppliers',
            'create_suppliers',
            'edit_suppliers',
            'delete_suppliers',

            // Purchase Order permissions
            'view_purchase_orders',
            'create_purchase_orders',
            'edit_purchase_orders',
            'delete_purchase_orders',
            'receive_purchase_orders',

            // Stock Adjustment permissions
            'view_stock_adjustments',
            'create_stock_adjustments',

            // Report permissions
            'view_reports',
            'export_reports',

            // User Management permissions
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_roles',

            // Notification permissions
            'view_notifications',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and assign permissions

        // 1. ADMIN ROLE - Full access to everything
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all()); // All permissions

        // 2. MANAGER ROLE - Inventory and supplier management, reports
        $managerRole = Role::create(['name' => 'Manager']);
        $managerRole->givePermissionTo([
            // Products
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',

            // Categories
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',

            // Suppliers
            'view_suppliers',
            'create_suppliers',
            'edit_suppliers',
            'delete_suppliers',

            // Purchase Orders
            'view_purchase_orders',
            'create_purchase_orders',
            'edit_purchase_orders',
            'delete_purchase_orders',
            'receive_purchase_orders',

            // Stock Adjustments
            'view_stock_adjustments',
            'create_stock_adjustments',

            // Reports
            'view_reports',
            'export_reports',

            // Notifications
            'view_notifications',
        ]);

        // 3. STAFF ROLE - View only + limited stock adjustments
        $staffRole = Role::create(['name' => 'Staff']);
        $staffRole->givePermissionTo([
            // Products (view only)
            'view_products',

            // Categories (view only)
            'view_categories',

            // Suppliers (view only)
            'view_suppliers',

            // Purchase Orders (view only)
            'view_purchase_orders',

            // Stock Adjustments (can view and create)
            'view_stock_adjustments',
            'create_stock_adjustments',

            // Reports (view only)
            'view_reports',

            // Notifications
            'view_notifications',
        ]);

        $this->command->info('✅ Roles and permissions created successfully!');
        $this->command->info('   - Admin: Full access');
        $this->command->info('   - Manager: Inventory & supplier management');
        $this->command->info('   - Staff: View only + stock adjustments');
    }
}
