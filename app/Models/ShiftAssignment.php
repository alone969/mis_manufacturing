<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Clock in the user for this shift assignment.
     */
    public function clockIn(): bool
    {
        if ($this->clock_in) {
            return false; // Already clocked in
        }

        $now = now();
        $shiftStart = $this->date->copy()->setTimeFromTimeString($this->shift->start_time);
        $isLate = $now->gt($shiftStart->addMinutes(5)); // 5-minute grace period

        $this->update([
            'clock_in' => $now,
            'status' => $isLate ? 'late' : 'present',
        ]);

        return true;
    }

    /**
     * Clock out the user for this shift assignment.
     */
    public function clockOut(): bool
    {
        if (! $this->clock_in || $this->clock_out) {
            return false; // Not clocked in or already clocked out
        }

        $this->update([
            'clock_out' => now(),
        ]);

        return true;
    }
}
