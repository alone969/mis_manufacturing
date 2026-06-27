<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Shift;
use App\Models\ShiftAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebShiftController extends Controller
{
    /**
     * List all shifts.
     */
    public function index(Request $request): View
    {
        $shifts = Shift::with('creator:id,name', 'assignments.user:id,name')
            ->orderByDesc('created_at')
            ->get();

        return view('shifts.index', compact('shifts'));
    }

    /**
     * Show create shift form.
     */
    public function create(): View
    {
        return view('shifts.create');
    }

    /**
     * Store shift.
     */
    public function store(Request $request)
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

        return redirect()->route('shifts.index')->with('success', 'Shift created successfully.');
    }

    /**
     * Show edit shift form.
     */
    public function edit(Shift $shift): View
    {
        return view('shifts.edit', compact('shift'));
    }

    /**
     * Update shift.
     */
    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
        ]);

        $shift->update($request->only(['name', 'start_time', 'end_time']));
        $this->logActivity('update', "Updated shift: {$shift->name}", Shift::class, $shift->id);

        return redirect()->route('shifts.index')->with('success', 'Shift updated successfully.');
    }

    /**
     * Delete shift.
     */
    public function destroy(Shift $shift)
    {
        $shiftName = $shift->name;
        $shift->delete();
        $this->logActivity('delete', "Deleted shift: {$shiftName}", Shift::class, $shift->id);

        return redirect()->route('shifts.index')->with('success', 'Shift deleted successfully.');
    }

    /**
     * Show assign form.
     */
    public function assignForm(Shift $shift): View
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        $existingAssignments = $shift->assignments()->with('user:id,name')->get();

        return view('shifts.assign', compact('shift', 'employees', 'existingAssignments'));
    }

    /**
     * Assign employees to shift.
     */
    public function assign(Request $request, Shift $shift)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'date' => 'required|date',
        ]);

        foreach ($request->user_ids as $userId) {
            ShiftAssignment::firstOrCreate(
                ['user_id' => $userId, 'shift_id' => $shift->id, 'date' => $request->date],
                ['status' => 'assigned']
            );

            Notification::create([
                'user_id' => $userId,
                'type' => 'shift_assigned',
                'title' => 'Shift Assigned',
                'message' => "You have been assigned to {$shift->name} on {$request->date}",
                'data' => ['shift_id' => $shift->id, 'date' => $request->date],
            ]);
        }

        $this->logActivity('assign', "Assigned employees to shift: {$shift->name}");

        return redirect()->route('shifts.index')->with('success', 'Employees assigned successfully.');
    }

    /**
     * Remove assignment.
     */
    public function unassign(ShiftAssignment $assignment)
    {
        $assignment->delete();
        $this->logActivity('unassign', 'Removed employee from shift');

        return redirect()->back()->with('success', 'Assignment removed successfully.');
    }

    /**
     * Schedule view (daily, weekly, monthly).
     */
    public function schedule(Request $request): View
    {
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

        $assignments = $query->orderBy('date')->get();
        $shifts = Shift::all();

        return view('shifts.schedule', compact('assignments', 'shifts', 'date', 'period'));
    }
}
