<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device_name',
        'status',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the login log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a successful login attempt.
     */
    public static function logSuccess(User $user, ?string $ipAddress = null, ?string $userAgent = null, ?string $deviceName = null): static
    {
        return static::create([
            'user_id' => $user->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_name' => $deviceName,
            'status' => 'success',
        ]);
    }

    /**
     * Log a failed login attempt.
     */
    public static function logFailure(User $user, string $reason, ?string $ipAddress = null, ?string $userAgent = null, ?string $deviceName = null): static
    {
        return static::create([
            'user_id' => $user->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_name' => $deviceName,
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Scope to get successful logins only.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope to get failed logins only.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get recent logins.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
