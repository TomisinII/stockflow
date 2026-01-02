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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Who receives this notification
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade'); // Delete notifications if user deleted

            // Notification styling
            // 'success' = green (PO received)
            // 'warning' = amber (low stock)
            // 'danger' = red (out of stock)
            // 'info' = blue (general info)
            $table->enum('type', ['success', 'warning', 'danger', 'info']);

            // Notification content
            $table->string('title', 255); // "Low Stock Alert"
            $table->text('message'); // "Product XYZ is running low (5 units left)"

            // Additional data (JSON) for linking to relevant pages
            // Example: {"product_id": 123, "stock_level": 5, "minimum_stock": 10}
            $table->json('data')->nullable();

            // Read status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_read']); // Get unread notifications
            $table->index(['user_id', 'created_at']); // Get recent notifications
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
