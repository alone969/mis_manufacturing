<?php

namespace App\Http\Controllers;

use App\Models\ShiftAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Clock in the authenticated user for today's shift.
     */
    public function clockIn(Request $request): JsonResponse
    {
        $today = now()->toDateString();

        $assignment = ShiftAssignment::where('user_id', $request->user()->id)
            ->where('date', $today)
            ->first();

        if (! $assignment) {
            return $this->errorResponse('No shift assigned for today.', 404);
        }

        $result = $assignment->clockIn();

        if (! $result) {
            return $this->errorResponse('You have already clocked in for today.', 422);
        }

        $this->logActivity('clock_in', 'Clocked in for shift: ' . $assignment->shift->name);

        return response()->json([
            'message' => 'Clocked in successfully.',
            'assignment' => [
                'id' => $assignment->id,
                'shift' => $assignment->shift->name,
                'clock_in' => $assignment->fresh()->clock_in->format('H:i'),
                'status' => $assignment->fresh()->status,
            ],
        ]);
    }

    /**
     * Clock out the authenticated user.
     */
    public function clockOut(Request $request): JsonResponse
    {
        $today = now()->toDateString();

        $assignment = ShiftAssignment::where('user_id', $request->user()->id)
            ->where('date', $today)
            ->first();

        if (! $assignment) {
            return $this->errorResponse('No shift assigned for today.', 404);
        }

        $result = $assignment->clockOut();

        if (! $result) {
            return $this->errorResponse('You have not clocked in yet or already clocked out.', 422);
        }

        $this->logActivity('clock_out', 'Clocked out from shift: ' . $assignment->shift->name);

        return response()->json([
            'message' => 'Clocked out successfully.',
            'assignment' => [
                'id' => $assignment->id,
                'shift' => $assignment->shift->name,
                'clock_in' => $assignment->fresh()->clock_in?->format('H:i'),
                'clock_out' => $assignment->fresh()->clock_out?->format('H:i'),
                'status' => $assignment->fresh()->status,
            ],
        ]);
    }

    /**
     * Get attendance records for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = ShiftAssignment::with('shift:name,start_time,end_time');

        // Admin can see all attendance, others see only their own
        if ($user->role === 'employee') {
            $query->where('user_id', $user->id);
        } elseif ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Date filters
        if ($request->has('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }

        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->with('user:id,name')
            ->orderByDesc('date')
            ->paginate($request->get('per_page', 20));

        return response()->json($records);
    }
}
