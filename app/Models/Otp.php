<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code_hash',
        'type',
        'is_used',
        'expires_at',
        'used_at',
    ];

    protected $hidden = [
        'code_hash',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'is_used' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the OTP.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new OTP code for a user.
     *
     * @param int $length Length of the OTP code
     * @param int $expiryMinutes Minutes until OTP expires
     */
    public static function generate(User $user, string $type = 'login', int $length = 6, int $expiryMinutes = 5): array
    {
        // Invalidate any existing OTPs of this type for the user
        static::where('user_id', $user->id)
            ->where('type', $type)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // Generate numeric code
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= random_int(0, 9);
        }

        // Hash the code for storage
        $codeHash = hash('sha256', $code);

        // Create OTP record
        $otp = static::create([
            'user_id' => $user->id,
            'code_hash' => $codeHash,
            'type' => $type,
            'is_used' => false,
            'expires_at' => Carbon::now()->addMinutes($expiryMinutes),
        ]);

        return [
            'otp' => $otp,
            'code' => $code, // Return plain code for sending via email
        ];
    }

    /**
     * Verify an OTP code.
     */
    public static function verify(User $user, string $code, string $type = 'login'): bool
    {
        $otp = static::where('user_id', $user->id)
            ->where('type', $type)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if (!$otp) {
            return false;
        }

        $codeHash = hash('sha256', $code);

        if (!hash_equals($otp->code_hash, $codeHash)) {
            return false;
        }

        // Mark OTP as used
        $otp->update([
            'is_used' => true,
            'used_at' => Carbon::now(),
        ]);

        return true;
    }

    /**
     * Check if OTP is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Scope to get valid (unused and not expired) OTPs.
     */
    public function scopeValid($query)
    {
        return $query->where('is_used', false)
            ->where('expires_at', '>', Carbon::now());
    }
}
