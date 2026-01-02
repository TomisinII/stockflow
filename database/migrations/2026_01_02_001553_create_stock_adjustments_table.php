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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();

            // Which product was adjusted
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade'); // If product deleted, delete its history

            // Type of adjustment
            // 'in' = stock coming in (purchase, return)
            // 'out' = stock going out (sale, damage, theft)
            // 'correction' = manual correction from stocktake
            $table->enum('adjustment_type', ['in', 'out', 'correction']);

            // Quantity change (positive for 'in', negative for 'out')
            $table->integer('quantity');

            // Why this adjustment happened
            $table->string('reason', 255); // purchase, sale, damaged, expired, theft, stocktake

            // Reference to source document (optional but helpful)
            $table->string('reference', 100)->nullable(); // PO-2026-0001, INV-123

            // Additional context
            $table->text('notes')->nullable();

            // Who made this adjustment
            $table->foreignId('adjusted_by')
                ->constrained('users')
                ->onDelete('restrict'); // Keep record of who did it

            // When it happened
            $table->date('adjustment_date');

            // Timestamps
            $table->timestamps();

            // Indexes for reporting and filtering
            $table->index(['product_id', 'adjustment_date']);
            $table->index('adjusted_by');
            $table->index('adjustment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
