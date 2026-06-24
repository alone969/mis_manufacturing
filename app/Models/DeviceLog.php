<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_name',
        'ip_address',
        'user_agent',
        'is_current_device',
        'last_login_at',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'is_current_device' => 'boolean',
            'last_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the device log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record or update a device login.
     */
    public static function recordLogin(User $user, ?string $ipAddress = null, ?string $userAgent = null, ?string $deviceName = null): static
    {
        // Try to find existing device for this user agent
        $device = static::where('user_id', $user->id)
            ->where('user_agent', $userAgent)
            ->first();

        if ($device) {
            // Update existing device
            $device->update([
                'ip_address' => $ipAddress,
                'device_name' => $deviceName,
                'last_login_at' => now(),
                'last_activity_at' => now(),
                'is_current_device' => true,
            ]);

            // Mark other devices as not current
            static::where('user_id', $user->id)
                ->where('id', '!=', $device->id)
                ->update(['is_current_device' => false]);

            return $device;
        }

        // Mark all other devices as not current
        static::where('user_id', $user->id)
            ->update(['is_current_device' => false]);

        // Create new device record
        return static::create([
            'user_id' => $user->id,
            'device_name' => $deviceName,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'is_current_device' => true,
            'last_login_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Update the last activity timestamp.
     */
    public function touchActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Scope to get current device only.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current_device', true);
    }

    /**
     * Scope to get devices for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
