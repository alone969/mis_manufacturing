<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('employee')->after('email');
            $table->boolean('is_email_verified')->default(false)->after('role');
            $table->string('onboarding_status')->default('pending')->after('is_email_verified');
            $table->json('settings')->nullable()->after('onboarding_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_email_verified', 'onboarding_status', 'settings']);
        });
    }
};
