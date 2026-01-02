<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table stores individual line items for each purchase order
     * Example: PO #001 might have 10 laptops, 20 mice, 5 keyboards
     */
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('purchase_order_id')
                ->constrained('purchase_orders')
                ->onDelete('cascade'); // If PO deleted, delete all its items

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('restrict'); // Can't delete product in active PO

            // Quantities
            $table->integer('quantity_ordered'); // How many we ordered
            $table->integer('quantity_received')->default(0); // How many we actually got

            // Pricing (snapshot at time of order - prices change over time!)
            $table->decimal('unit_cost', 10, 2); // Price per unit at order time
            $table->decimal('subtotal', 12, 2); // quantity × unit_cost

            // Timestamps
            $table->timestamps();

            // Composite index for faster lookups
            $table->index(['purchase_order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
