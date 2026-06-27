<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'type',
        'is_used',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_used' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate an OTP for the given user and type.
     */
    public static function generate(User $user, string $type, int $length = 6, int $ttlMinutes = 10): array
    {
        // Invalidate any existing OTPs of the same type
        static::where('user_id', $user->id)
            ->where('type', $type)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        $code = str_pad((string) random_int(0, 999999), $length, '0', STR_PAD_LEFT);

        $otp = static::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => $type,
            'is_used' => false,
            'expires_at' => now()->addMinutes($ttlMinutes),
        ]);

        return ['otp' => $otp, 'code' => $code];
    }

    /**
     * Verify an OTP code.
     */
    public static function verify(User $user, string $code, string $type): bool
    {
        $otp = static::where('user_id', $user->id)
            ->where('code', $code)
            ->where('type', $type)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otp) {
            return false;
        }

        $otp->update(['is_used' => true]);

        return true;
    }
}
