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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Company Information
            $table->string('company_name')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_website')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_logo')->nullable();

            // Notifications
            $table->boolean('email_low_stock_alerts')->default(true);
            $table->boolean('email_order_received')->default(true);
            $table->boolean('email_daily_summary')->default(false);
            $table->boolean('push_low_stock_alerts')->default(true);
            $table->boolean('push_order_updates')->default(true);
            $table->string('low_stock_threshold')->default('20');

            // Appearance
            $table->string('theme')->default('system');
            $table->string('language')->default('en');
            $table->string('date_format')->default('DD/MM/YYYY');
            $table->string('currency')->default('NGN');

            // Security
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('session_timeout')->default('30');
            $table->string('password_expiry')->default('90');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
