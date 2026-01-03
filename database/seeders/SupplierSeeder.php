<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates sample suppliers for testing
     */
    public function run(): void
    {
        $suppliers = [
            [
                'company_name' => 'Tech Solutions Ltd',
                'contact_person' => 'Michael Chen',
                'email' => 'sales@techsolutions.com',
                'phone' => '+234 801 234 5678',
                'address' => '15 Admiralty Way',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'zip_code' => '101241',
                'country' => 'Nigeria',
                'payment_terms' => 'Net 30',
                'status' => 'active',
                'notes' => 'Primary electronics supplier. Fast delivery.',
            ],
            [
                'company_name' => 'Global Fashion Imports',
                'contact_person' => 'Sarah Johnson',
                'email' => 'orders@globalfashion.com',
                'phone' => '+234 802 345 6789',
                'address' => '42 Allen Avenue',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'zip_code' => '101233',
                'country' => 'Nigeria',
                'payment_terms' => 'Net 60',
                'status' => 'active',
                'notes' => 'Quality clothing supplier. Ships from Dubai.',
            ],
            [
                'company_name' => 'Office Pro Nigeria',
                'contact_person' => 'David Okafor',
                'email' => 'info@officepro.ng',
                'phone' => '+234 803 456 7890',
                'address' => '8 Ikeja Way',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'zip_code' => '100001',
                'country' => 'Nigeria',
                'payment_terms' => 'COD',
                'status' => 'active',
                'notes' => 'Office supplies and stationery. Same-day delivery available.',
            ],
            [
                'company_name' => 'FoodMart Distributors',
                'contact_person' => 'Amaka Williams',
                'email' => 'supply@foodmart.com',
                'phone' => '+234 804 567 8901',
                'address' => '23 Market Road',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'zip_code' => '101245',
                'country' => 'Nigeria',
                'payment_terms' => 'Net 15',
                'status' => 'active',
                'notes' => 'Fresh produce and packaged goods. Weekly deliveries.',
            ],
            [
                'company_name' => 'Industrial Supplies Co',
                'contact_person' => 'Chidi Nwankwo',
                'email' => 'sales@industrialsupplies.com',
                'phone' => '+234 805 678 9012',
                'address' => '56 Industrial Estate',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'zip_code' => '102214',
                'country' => 'Nigeria',
                'payment_terms' => 'Net 45',
                'status' => 'active',
                'notes' => 'Raw materials and manufacturing supplies. Bulk orders only.',
            ],
            [
                'company_name' => 'Beauty World Imports',
                'contact_person' => 'Fatima Hassan',
                'email' => 'orders@beautyworld.ng',
                'phone' => '+234 806 789 0123',
                'address' => '12 Victoria Island',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'zip_code' => '101241',
                'country' => 'Nigeria',
                'payment_terms' => 'Net 30',
                'status' => 'active',
                'notes' => 'Cosmetics and personal care products. International brands.',
            ],
            [
                'company_name' => 'Discontinued Supplier Ltd',
                'contact_person' => 'Old Contact',
                'email' => 'old@supplier.com',
                'phone' => '+234 807 890 1234',
                'address' => 'Old Address',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'zip_code' => '100001',
                'country' => 'Nigeria',
                'payment_terms' => 'Net 30',
                'status' => 'inactive',
                'notes' => 'No longer in business. Kept for historical records.',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        $this->command->info('✅ Suppliers created successfully!');
        $this->command->info('   - 6 active suppliers');
        $this->command->info('   - 1 inactive supplier (for testing)');
    }
}
