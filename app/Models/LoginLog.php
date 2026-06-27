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
        'status',
        'failure_reason',
        'ip_address',
        'user_agent',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'attempted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a successful login.
     */
    public static function logSuccess(User $user, ?string $ip, ?string $agent): static
    {
        return static::create([
            'user_id' => $user->id,
            'status' => 'success',
            'ip_address' => $ip,
            'user_agent' => $agent,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Log a failed login attempt.
     */
    public static function logFailure(User $user, string $reason, ?string $ip, ?string $agent): static
    {
        return static::create([
            'user_id' => $user->id,
            'status' => 'failed',
            'failure_reason' => $reason,
            'ip_address' => $ip,
            'user_agent' => $agent,
            'attempted_at' => now(),
        ]);
    }
}
