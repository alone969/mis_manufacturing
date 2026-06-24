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
            $table->enum('role', ['employee', 'manager', 'admin'])->default('employee')->after('name');
            $table->boolean('is_email_verified')->default(false)->after('email_verified_at');
            $table->enum('onboarding_status', ['pending', 'in_progress', 'completed'])->default('pending')->after('is_email_verified');
            $table->json('settings')->nullable()->after('onboarding_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'is_email_verified',
                'onboarding_status',
                'settings',
            ]);
        });
    }
};
