<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates sample products across different categories
     */
    public function run(): void
    {
        // Get categories and suppliers
        $electronics = Category::where('name', 'Electronics')->first();
        $laptops = Category::where('name', 'Laptops')->first();
        $smartphones = Category::where('name', 'Smartphones')->first();
        $clothing = Category::where('name', 'Clothing')->first();
        $food = Category::where('name', 'Food & Beverage')->first();
        $office = Category::where('name', 'Office Supplies')->first();
        $beauty = Category::where('name', 'Beauty & Personal Care')->first();

        $techSupplier = Supplier::where('company_name', 'Tech Solutions Ltd')->first();
        $fashionSupplier = Supplier::where('company_name', 'Global Fashion Imports')->first();
        $officeSupplier = Supplier::where('company_name', 'Office Pro Nigeria')->first();
        $foodSupplier = Supplier::where('company_name', 'FoodMart Distributors')->first();
        $beautySupplier = Supplier::where('company_name', 'Beauty World Imports')->first();

        $products = [
            // Electronics - Good stock
            [
                'name' => 'Dell Latitude 5420 Laptop',
                'sku' => 'LAP-DELL-5420',
                'barcode' => '4712345678901',
                'description' => '14" FHD, Intel i5, 8GB RAM, 256GB SSD',
                'category_id' => $laptops->id,
                'supplier_id' => $techSupplier->id,
                'unit_of_measure' => 'pieces',
                'cost_price' => 450000,
                'selling_price' => 550000,
                'current_stock' => 25,
                'minimum_stock' => 10,
                'maximum_stock' => 50,
                'status' => 'active',
            ],
            [
                'name' => 'HP EliteBook 840 G8',
                'sku' => 'LAP-HP-840G8',
                'barcode' => '4712345678902',
                'description' => '14" FHD, Intel i7, 16GB RAM, 512GB SSD',
                'category_id' => $laptops->id,
                'supplier_id' => $techSupplier->id,
                'unit_of_measure' => 'pieces',
                'cost_price' => 650000,
                'selling_price' => 800000,
                'current_stock' => 15,
                'minimum_stock' => 8,
                'maximum_stock' => 30,
                'status' => 'active',
            ],
            // Smartphones - Low stock (warning)
            [
                'name' => 'Samsung Galaxy S23',
                'sku' => 'PHN-SAM-S23',
                'barcode' => '4712345678903',
                'description' => '6.1" AMOLED, 8GB RAM, 256GB Storage',
                'category_id' => $smartphones->id,
                'supplier_id' => $techSupplier->id,
                'unit_of_measure' => 'pieces',
                'cost_price' => 420000,
                'selling_price' => 520000,
                'current_stock' => 5, // LOW STOCK
                'minimum_stock' => 10,
                'maximum_stock' => 40,
                'status' => 'active',
            ],
            [
                'name' => 'iPhone 15 Pro',
                'sku' => 'PHN-APL-15PRO',
                'barcode' => '4712345678904',
                'description' => '6.1" Super Retina XDR, A17 Pro chip, 256GB',
                'category_id' => $smartphones->id,
                'supplier_id' => $techSupplier->id,
                'unit_of_measure' => 'pieces',
                'cost_price' => 750000,
                'selling_price' => 900000,
                'current_stock' => 0, // OUT OF STOCK
                'minimum_stock' => 5,
                'maximum_stock' => 20,
                'status' => 'active',
            ],
            // Electronics accessories
            [
                'name' => 'Logitech MX Master 3S Mouse',
                'sku' => 'ACC-LOG-MX3S',
                'barcode' => '4712345678905',
                'description' => 'Wireless ergonomic mouse',
                'category_id' => $electronics->id,
                'supplier_id' => $techSupplier->id,
                'unit_of_measure' => 'pieces',
                'cost_price' => 35000,
                'selling_price' => 45000,
                'current_stock' => 50,
                'minimum_stock' => 20,
                'maximum_stock' => 100,
                'status' => 'active',
            ],
            [
                'name' => 'Anker USB-C Cable (2m)',
                'sku' => 'CAB-ANK-USBC-2M',
                'barcode' => '4712345678906',
                'description' => 'Fast charging USB-C to USB-C cable',
                'category_id' => $electronics->id,
                'supplier_id' => $techSupplier->id,
                'unit_of_measure' => 'pieces',
                'cost_price' => 2500,
                'selling_price' => 4000,
                'current_stock' => 3, // LOW STOCK
                'minimum_stock' => 30,
                'maximum_stock' => 200,
                'status' => 'active',
            ],
            // Clothing
            [
                'name' => 'Men\'s Cotton T-Shirt (Blue)',
                'sku' => 'CLO-TSH-MEN-BLU',
                'barcode' => '4712345678907',
                'description' => '100% cotton, size L',
                'category_id' => $clothing->id,
                'supplier_id' => $fashionSupplier->id,
                'unit_of_measure' => 'pieces',
                'cost_price' => 3000,
                'selling_price' => 5500,
                'current_stock' => 120,
                'minimum_stock' => 50,
                'maximum_stock' => 300,
                'status' => 'active',
            ],
            [
                'name' => 'Women\'s Jeans (Size 32)',
                'sku' => 'CLO-JEA-WOM-32',
                'barcode' => '4712345678908',
                'description' => 'Slim fit denim jeans',
                'category_id' => $clothing->id,
                'supplier_id' => $fashionSupplier->id,
                'unit_of_measure' => 'pieces',
                'cost_price' => 8000,
                'selling_price' => 12000,
                'current_stock' => 45,
                'minimum_stock' => 20,
                'maximum_stock' => 100,
                'status' => 'active',
            ],
            // Office Supplies
            [
                'name' => 'A4 Printing Paper (500 sheets)',
                'sku' => 'OFF-PAP-A4-500',
                'barcode' => '4712345678909',
                'description' => '80gsm white copier paper',
                'category_id' => $office->id,
                'supplier_id' => $officeSupplier->id,
                'unit_of_measure' => 'reams',
                'cost_price' => 2000,
                'selling_price' => 3000,
                'current_stock' => 200,
                'minimum_stock' => 100,
                'maximum_stock' => 500,
                'status' => 'active',
            ],
            [
                'name' => 'Ballpoint Pen (Black) - Box of 50',
                'sku' => 'OFF-PEN-BLK-50',
                'barcode' => '4712345678910',
                'description' => 'Medium point ballpoint pens',
                'category_id' => $office->id,
                'supplier_id' => $officeSupplier->id,
                'unit_of_measure' => 'boxes',
                'cost_price' => 5000,
                'selling_price' => 7500,
                'current_stock' => 8, // LOW STOCK
                'minimum_stock' => 15,
                'maximum_stock' => 50,
                'status' => 'active',
            ],
            // Food & Beverage
            [
                'name' => 'Bottled Water (500ml) - Pack of 12',
                'sku' => 'FOD-WAT-500-12',
                'barcode' => '4712345678911',
                'description' => 'Pure drinking water',
                'category_id' => $food->id,
                'supplier_id' => $foodSupplier->id,
                'unit_of_measure' => 'packs',
                'cost_price' => 800,
                'selling_price' => 1200,
                'current_stock' => 300,
                'minimum_stock' => 100,
                'maximum_stock' => 500,
                'status' => 'active',
            ],
            [
                'name' => 'Coffee Beans (1kg)',
                'sku' => 'FOD-COF-BEA-1KG',
                'barcode' => '4712345678912',
                'description' => 'Premium Arabica coffee beans',
                'category_id' => $food->id,
                'supplier_id' => $foodSupplier->id,
                'unit_of_measure' => 'kg',
                'cost_price' => 4500,
                'selling_price' => 7000,
                'current_stock' => 0, // OUT OF STOCK
                'minimum_stock' => 20,
                'maximum_stock' => 100,
                'status' => 'active',
            ],
            // Beauty Products
            [
                'name' => 'Face Moisturizer SPF 30',
                'sku' => 'BEA-MOI-SPF30',
                'barcode' => '4712345678913',
                'description' => 'Daily moisturizer with sun protection',
                'category_id' => $beauty->id,
                'supplier_id' => $beautySupplier->id,
                'unit_of_measure' => 'pieces',
                'cost_price' => 3500,
                'selling_price' => 6000,
                'current_stock' => 35,
                'minimum_stock' => 20,
                'maximum_stock' => 80,
                'status' => 'active',
            ],
            [
                'name' => 'Shampoo (500ml)',
                'sku' => 'BEA-SHA-500',
                'barcode' => '4712345678914',
                'description' => 'Nourishing hair shampoo',
                'category_id' => $beauty->id,
                'supplier_id' => $beautySupplier->id,
                'unit_of_measure' => 'bottles',
                'cost_price' => 2000,
                'selling_price' => 3500,
                'current_stock' => 6, // LOW STOCK
                'minimum_stock' => 25,
                'maximum_stock' => 100,
                'status' => 'active',
            ],
            // Inactive product (for testing)
            [
                'name' => 'Discontinued Product',
                'sku' => 'DIS-PRD-001',
                'barcode' => '4712345678915',
                'description' => 'No longer sold',
                'category_id' => $electronics->id,
                'supplier_id' => $techSupplier->id,
                'unit_of_measure' => 'pieces',
                'cost_price' => 10000,
                'selling_price' => 15000,
                'current_stock' => 0,
                'minimum_stock' => 0,
                'maximum_stock' => 0,
                'status' => 'inactive',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('✅ Products created successfully!');
        $this->command->info('   - 15 products across multiple categories');
        $this->command->info('   - Mix of stock levels: good, low, and out of stock');
        $this->command->info('   - 1 inactive product');
    }
}
