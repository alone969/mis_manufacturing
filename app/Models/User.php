<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_email_verified', 'onboarding_status', 'settings'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_email_verified' => 'boolean',
            'settings' => 'array',
        ];
    }

    /**
     * Get the OTPs for the user.
     */
    public function otps(): HasMany
    {
        return $this->hasMany(Otp::class);
    }

    /**
     * Get the login logs for the user.
     */
    public function loginLogs(): HasMany
    {
        return $this->hasMany(LoginLog::class);
    }

    /**
     * Get the device logs for the user.
     */
    public function deviceLogs(): HasMany
    {
        return $this->hasMany(DeviceLog::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a manager.
     */
    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    /**
     * Check if user is an employee.
     */
    public function isEmployee(): bool
    {
        return $this->hasRole('employee');
    }

    /**
     * Check if user's email is verified.
     */
    public function isEmailVerified(): bool
    {
        return $this->is_email_verified;
    }

    /**
     * Mark user's email as verified.
     */
    public function markEmailAsVerified(): void
    {
        $this->update(['is_email_verified' => true]);
    }

    /**
     * Check if onboarding is completed.
     */
    public function isOnboardingCompleted(): bool
    {
        return $this->onboarding_status === 'completed';
    }

    /**
     * Get the current active device.
     */
    public function currentDevice(): ?DeviceLog
    {
        return $this->deviceLogs()->current()->first();
    }
}
