<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Shift;
use App\Models\ShiftAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * List all shifts.
     */
    public function index(): JsonResponse
    {
        $shifts = Shift::with('creator:id,name')
            ->withCount('assignments')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($shifts);
    }

    /**
     * Create a new shift.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $shift = Shift::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'created_by' => $request->user()->id,
        ]);

        ActivityLog::log(
            $request->user()->id,
            'shift_created',
            Shift::class,
            $shift->id,
            "Created shift: {$shift->name}",
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json($shift->load('creator:id,name'), 201);
    }

    /**
     * Update a shift.
     */
    public function update(Request $request, Shift $shift): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
        ]);

        $shift->update($request->only(['name', 'start_time', 'end_time']));

        ActivityLog::log(
            $request->user()->id,
            'shift_updated',
            Shift::class,
            $shift->id,
            "Updated shift: {$shift->name}",
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json($shift->load('creator:id,name'));
    }

    /**
     * Delete a shift.
     */
    public function destroy(Request $request, Shift $shift): JsonResponse
    {
        $name = $shift->name;
        $shift->delete();

        ActivityLog::log(
            $request->user()->id,
            'shift_deleted',
            Shift::class,
            $shift->id,
            "Deleted shift: {$name}",
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json(['message' => 'Shift deleted successfully.']);
    }

    /**
     * Assign an employee to a shift.
     */
    public function assign(Request $request, Shift $shift): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $existing = ShiftAssignment::where('shift_id', $shift->id)
            ->where('user_id', $request->user_id)
            ->where('date', $request->date)
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'Employee is already assigned to this shift for this date.'], 422);
        }

        $assignment = ShiftAssignment::create([
            'shift_id' => $shift->id,
            'user_id' => $request->user_id,
            'date' => $request->date,
            'status' => 'scheduled',
        ]);

        ActivityLog::log(
            $request->user()->id,
            'shift_assigned',
            ShiftAssignment::class,
            $assignment->id,
            "Assigned user #{$request->user_id} to shift: {$shift->name} on {$request->date}",
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json($assignment->load('shift:name', 'user:id,name'), 201);
    }

    /**
     * Remove an assignment.
     */
    public function unassign(Request $request, Shift $shift, ShiftAssignment $assignment): JsonResponse
    {
        $assignment->delete();

        return response()->json(['message' => 'Assignment removed.']);
    }

    /**
     * Get assignments for a specific date.
     */
    public function assignments(Request $request, ?string $date = null): JsonResponse
    {
        $date = $date ?? now()->toDateString();

        $assignments = ShiftAssignment::with('shift:name,start_time,end_time', 'user:id,name')
            ->where('date', $date)
            ->get();

        return response()->json($assignments);
    }

    /**
     * Clock in for a shift assignment.
     */
    public function clockIn(Request $request, ShiftAssignment $assignment): JsonResponse
    {
        if ($assignment->status !== 'scheduled') {
            return response()->json(['message' => 'Cannot clock in — already ' . $assignment->status . '.'], 422);
        }

        $assignment->clockIn();

        ActivityLog::log(
            $request->user()->id,
            'clock_in',
            ShiftAssignment::class,
            $assignment->id,
            'Clocked in for shift on ' . $assignment->date->toDateString(),
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json($assignment->fresh()->load('shift:name'));
    }

    /**
     * Clock out for a shift assignment.
     */
    public function clockOut(Request $request, ShiftAssignment $assignment): JsonResponse
    {
        if ($assignment->status !== 'clocked_in') {
            return response()->json(['message' => 'Cannot clock out — not clocked in.'], 422);
        }

        $assignment->clockOut();

        ActivityLog::log(
            $request->user()->id,
            'clock_out',
            ShiftAssignment::class,
            $assignment->id,
            'Clocked out for shift on ' . $assignment->date->toDateString(),
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json($assignment->fresh()->load('shift:name'));
    }
}
