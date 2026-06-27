<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Shift;
use App\Models\ShiftAssignment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * List all shifts.
     */
    public function index(Request $request): JsonResponse
    {
        $shifts = Shift::with('creator:id,name', 'assignments.user:id,name')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($shifts);
    }

    /**
     * Create a new shift (admin/manager).
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

        $this->logActivity('create', "Created shift: {$shift->name}", Shift::class, $shift->id);

        return response()->json([
            'message' => 'Shift created successfully.',
            'shift' => $shift,
        ], 201);
    }

    /**
     * Update a shift (admin/manager).
     */
    public function update(Request $request, Shift $shift): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
        ]);

        $shift->update($request->only(['name', 'start_time', 'end_time']));

        $this->logActivity('update', "Updated shift: {$shift->name}", Shift::class, $shift->id);

        return response()->json([
            'message' => 'Shift updated successfully.',
            'shift' => $shift,
        ]);
    }

    /**
     * Delete a shift (admin).
     */
    public function destroy(Shift $shift): JsonResponse
    {
        $shiftName = $shift->name;
        $shift->delete();

        $this->logActivity('delete', "Deleted shift: {$shiftName}", Shift::class, $shift->id);

        return response()->json([
            'message' => 'Shift deleted successfully.',
        ]);
    }

    /**
     * Assign employees to a shift (admin/manager).
     */
    public function assign(Request $request): JsonResponse
    {
        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'date' => 'required|date',
        ]);

        $assigned = [];
        foreach ($request->user_ids as $userId) {
            $assignment = ShiftAssignment::firstOrCreate(
                [
                    'user_id' => $userId,
                    'shift_id' => $request->shift_id,
                    'date' => $request->date,
                ],
                ['status' => 'assigned']
            );
            $assigned[] = $assignment;
        }

        $this->logActivity('assign', 'Assigned employees to shift');

        // Create notifications for assigned employees
        foreach ($request->user_ids as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => 'shift_assigned',
                'title' => 'Shift Assigned',
                'message' => 'You have been assigned to a shift on ' . $request->date,
                'data' => ['shift_id' => $request->shift_id, 'date' => $request->date],
            ]);
        }

        return response()->json([
            'message' => 'Employees assigned successfully.',
            'assignments' => $assigned,
        ], 201);
    }

    /**
     * Remove a shift assignment.
     */
    public function unassign(ShiftAssignment $assignment): JsonResponse
    {
        $assignment->delete();

        $this->logActivity('unassign', 'Removed employee from shift');

        return response()->json([
            'message' => 'Assignment removed successfully.',
        ]);
    }

    /**
     * Get shift schedule (daily, weekly, monthly).
     */
    public function schedule(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'sometimes|in:daily,weekly,monthly',
            'date' => 'sometimes|date',
        ]);

        $date = $request->get('date', now()->toDateString());
        $period = $request->get('period', 'weekly');

        $query = ShiftAssignment::with('shift:name,start_time,end_time', 'user:id,name,role');

        match ($period) {
            'daily' => $query->whereDate('date', $date),
            'weekly' => $query->whereBetween('date', [
                now()->parse($date)->startOfWeek()->toDateString(),
                now()->parse($date)->endOfWeek()->toDateString(),
            ]),
            'monthly' => $query->whereMonth('date', now()->parse($date)->month)
                ->whereYear('date', now()->parse($date)->year),
        };

        $assignments = $query->orderBy('date')
            ->get();

        return response()->json($assignments);
    }
}
