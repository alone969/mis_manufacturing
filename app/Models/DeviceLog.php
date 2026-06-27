<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

class DeviceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_name',
        'browser',
        'operating_system',
        'ip_address',
        'user_agent',
        'last_login_at',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get logs for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Record a device login.
     */
    public static function recordLogin(User $user, ?string $ip, ?string $agent): static
    {
        // Parse user agent
        $browser = self::parseBrowser($agent);
        $os = self::parseOS($agent);

        // Find existing device log for this user + browser + OS combination
        $existing = static::where('user_id', $user->id)
            ->where('browser', $browser)
            ->where('operating_system', $os)
            ->first();

        if ($existing) {
            $existing->update([
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'ip_address' => $ip,
            ]);

            return $existing;
        }

        return static::create([
            'user_id' => $user->id,
            'browser' => $browser,
            'operating_system' => $os,
            'ip_address' => $ip,
            'user_agent' => $agent,
            'last_login_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Parse browser name from user agent.
     */
    private static function parseBrowser(?string $agent): string
    {
        if (! $agent) {
            return 'Unknown';
        }

        return match (true) {
            str_contains($agent, 'Firefox') => 'Firefox',
            str_contains($agent, 'Edg') => 'Edge',
            str_contains($agent, 'Chrome') => 'Chrome',
            str_contains($agent, 'Safari') && ! str_contains($agent, 'Chrome') => 'Safari',
            str_contains($agent, 'Opera') || str_contains($agent, 'OPR') => 'Opera',
            default => 'Other',
        };
    }

    /**
     * Parse OS name from user agent.
     */
    private static function parseOS(?string $agent): string
    {
        if (! $agent) {
            return 'Unknown';
        }

        return match (true) {
            str_contains($agent, 'Windows') => 'Windows',
            str_contains($agent, 'Mac OS') => 'macOS',
            str_contains($agent, 'Linux') => 'Linux',
            str_contains($agent, 'Android') => 'Android',
            str_contains($agent, 'iPhone') || str_contains($agent, 'iPad') => 'iOS',
            default => 'Other',
        };
    }
}
