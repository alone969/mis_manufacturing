<?php

namespace App\Http\Controllers;

use App\Models\ShiftAssignment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebAttendanceController extends Controller
{
    /**
     * Clock in the authenticated user.
     */
    public function clockIn(Request $request)
    {
        $today = now()->toDateString();
        $assignment = ShiftAssignment::where('user_id', $request->user()->id)
            ->where('date', $today)->first();

        if (! $assignment) {
            return redirect()->back()->with('error', 'No shift assigned for today.');
        }

        $result = $assignment->clockIn();

        if (! $result) {
            return redirect()->back()->with('error', 'You have already clocked in for today.');
        }

        $this->logActivity('clock_in', 'Clocked in for shift: ' . $assignment->shift->name);

        return redirect()->route('dashboard')->with('success', 'Clocked in successfully.');
    }

    /**
     * Clock out the authenticated user.
     */
    public function clockOut(Request $request)
    {
        $today = now()->toDateString();
        $assignment = ShiftAssignment::where('user_id', $request->user()->id)
            ->where('date', $today)->first();

        if (! $assignment) {
            return redirect()->back()->with('error', 'No shift assigned for today.');
        }

        $result = $assignment->clockOut();

        if (! $result) {
            return redirect()->back()->with('error', 'You have not clocked in yet or already clocked out.');
        }

        $this->logActivity('clock_out', 'Clocked out from shift: ' . $assignment->shift->name);

        return redirect()->route('dashboard')->with('success', 'Clocked out successfully.');
    }

    /**
     * View attendance records.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = ShiftAssignment::with('shift', 'user');

        if ($user->role === 'employee') {
            $query->where('user_id', $user->id);
        } elseif ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderByDesc('date')->paginate(20);

        return view('attendance.index', compact('records'));
    }
}
