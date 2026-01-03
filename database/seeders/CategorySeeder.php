<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates default product categories with icons and colors
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
                'icon' => 'device-mobile',
                'color' => '#3B82F6', // Blue
            ],
            [
                'name' => 'Clothing',
                'description' => 'Apparel and fashion items',
                'icon' => 'shopping-bag',
                'color' => '#EC4899', // Pink
            ],
            [
                'name' => 'Food & Beverage',
                'description' => 'Food items and drinks',
                'icon' => 'cake',
                'color' => '#F59E0B', // Amber
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Stationery and office equipment',
                'icon' => 'clipboard',
                'color' => '#8B5CF6', // Purple
            ],
            [
                'name' => 'Raw Materials',
                'description' => 'Manufacturing and production materials',
                'icon' => 'cube',
                'color' => '#6B7280', // Gray
            ],
            [
                'name' => 'Furniture',
                'description' => 'Home and office furniture',
                'icon' => 'home',
                'color' => '#10B981', // Green
            ],
            [
                'name' => 'Beauty & Personal Care',
                'description' => 'Cosmetics and personal care products',
                'icon' => 'sparkles',
                'color' => '#F472B6', // Light Pink
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Sports equipment and outdoor gear',
                'icon' => 'trophy',
                'color' => '#EF4444', // Red
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create some hierarchical categories (parent-child)
        $electronics = Category::where('name', 'Electronics')->first();

        Category::create([
            'name' => 'Laptops',
            'description' => 'Laptop computers',
            'icon' => 'computer-desktop',
            'color' => '#3B82F6',
            'parent_id' => $electronics->id,
        ]);

        Category::create([
            'name' => 'Smartphones',
            'description' => 'Mobile phones',
            'icon' => 'device-phone-mobile',
            'color' => '#3B82F6',
            'parent_id' => $electronics->id,
        ]);

        $this->command->info('✅ Categories created successfully!');
        $this->command->info('   - 8 parent categories');
        $this->command->info('   - 2 child categories under Electronics');
    }
}
