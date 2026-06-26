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
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
        ];
    }

    /**
     * Get the user this assignment belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the shift this assignment belongs to.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Clock in the employee.
     */
    public function clockIn(): void
    {
        $this->update([
            'clock_in' => now(),
            'status' => 'clocked_in',
        ]);
    }

    /**
     * Clock out the employee.
     */
    public function clockOut(): void
    {
        $this->update([
            'clock_out' => now(),
            'status' => 'clocked_out',
        ]);
    }
}
