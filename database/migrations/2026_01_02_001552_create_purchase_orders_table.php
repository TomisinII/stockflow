s<?php

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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            // PO identification
            $table->string('po_number', 50)->unique(); // PO-2026-0001

            // Supplier relationship
            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->onDelete('restrict'); // Can't delete supplier with active POs

            // Order dates
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();

            // Status workflow: draft → sent → received → cancelled
            $table->enum('status', ['draft', 'sent', 'received', 'cancelled'])
                ->default('draft');

            // Financial
            $table->decimal('total_amount', 12, 2);

            // Additional information
            $table->text('notes')->nullable();

            // User tracking
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('restrict'); // Keep record of who created it

            $table->foreignId('received_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null'); // Who received the goods

            $table->timestamp('received_at')->nullable(); // When goods were received

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('supplier_id');
            $table->index('status');
            $table->index('order_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
