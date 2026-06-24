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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code_hash'); // Hashed OTP code
            $table->string('type')->default('login'); // login, verification, password_reset
            $table->boolean('is_used')->default(false);
            $table->timestamp('expires_at'); // OTP expires after 5 minutes
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type', 'is_used']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
