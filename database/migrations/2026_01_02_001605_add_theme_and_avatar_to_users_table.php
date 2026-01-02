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
        Schema::table('users', function (Blueprint $table) {
            // Avatar image path (stored in storage/app/public/avatars)
            $table->string('avatar', 255)->nullable()->after('email');

            // Theme preference (light, dark, or system)
            $table->enum('theme', ['light', 'dark', 'system'])
                ->default('light')
                ->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
           $table->dropColumn(['avatar', 'theme']);
        });
    }
};
