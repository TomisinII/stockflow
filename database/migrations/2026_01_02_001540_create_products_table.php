<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Basic product information
            $table->string('name', 255);
            $table->string('sku', 100)->unique(); // Stock Keeping Unit
            $table->string('barcode', 100)->unique()->nullable();
            $table->text('description')->nullable();

            // Categorization and supplier
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('restrict'); // Can't delete category if products exist

            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('suppliers')
                ->onDelete('set null'); // If supplier deleted, set to null

            // Measurement and pricing
            $table->string('unit_of_measure', 50); // pieces, kg, liters, boxes
            $table->decimal('cost_price', 10, 2); // What we buy it for
            $table->decimal('selling_price', 10, 2); // What we sell it for

            // Stock management
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(10); // Reorder point
            $table->integer('maximum_stock')->nullable(); // Max capacity

            // Media
            $table->string('image_path', 255)->nullable();

            // Status
            $table->enum('status', ['active', 'inactive'])->default('active');

            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes(); // For archiving products

            // Indexes for performance
            $table->index('category_id');
            $table->index('supplier_id');
            $table->index('current_stock'); // For low-stock queries
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
