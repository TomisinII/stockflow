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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Category details
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->default('tag');
            $table->string('color', 20)->default('#6B7280'); // Gray-500

            // Hierarchical support (parent-child categories)
            // Example: Electronics > Laptops > Gaming Laptops
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->onDelete('cascade'); // If parent deleted, delete children too

            $table->timestamps();

            // Indexes for faster queries
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
