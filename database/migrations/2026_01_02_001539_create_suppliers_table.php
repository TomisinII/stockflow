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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

           // Company information
            $table->string('company_name', 255);
            $table->string('contact_person', 255)->nullable();

            // Contact details
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();

            // Address information
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->string('country', 100)->default('Nigeria');

            // Business terms
            $table->string('payment_terms', 100)->nullable(); // Net 30, Net 60, COD

            // Status management
            $table->enum('status', ['active', 'inactive'])->default('active');

            // Additional notes
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index('company_name');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
