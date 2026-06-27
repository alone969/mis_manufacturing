<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebSalaryController extends Controller
{
    /**
     * List salary records (admin).
     */
    public function index(Request $request): View
    {
        $query = Salary::with('user:id,name,email', 'processor:id,name');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $salaries = $query->orderByDesc('period_start')->paginate(20);
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('salaries.index', compact('salaries', 'employees'));
    }

    /**
     * Show process salary form (admin).
     */
    public function create(): View
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('salaries.create', compact('employees'));
    }

    /**
     * Process salary (admin).
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'notes' => 'nullable|string|max:1000',
        ]);

        $existing = Salary::where('user_id', $request->user_id)
            ->where('period_start', $request->period_start)
            ->where('period_end', $request->period_end)
            ->first();

        if ($existing) {
            return back()->withErrors(['period_start' => 'Salary already processed for this period.']);
        }

        $salary = Salary::create([
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'status' => 'pending',
            'processed_by' => $request->user()->id,
            'notes' => $request->notes,
        ]);

        Notification::create([
            'user_id' => $request->user_id,
            'type' => 'salary_processed',
            'title' => 'Salary Processed',
            'message' => "Your salary for {$request->period_start} to {$request->period_end} has been processed.",
            'data' => ['salary_id' => $salary->id, 'amount' => $request->amount],
        ]);

        $this->logActivity('salary_process', "Processed salary for user ID: {$request->user_id}", Salary::class, $salary->id);

        return redirect()->route('salaries.index')->with('success', 'Salary processed successfully.');
    }

    /**
     * Mark salary as paid (admin).
     */
    public function pay(Salary $salary)
    {
        $result = $salary->markAsPaid();

        if (! $result) {
            return back()->with('error', 'Salary has already been paid or failed.');
        }

        $this->logActivity('salary_pay', "Marked salary as paid for user ID: {$salary->user_id}", Salary::class, $salary->id);

        return redirect()->route('salaries.index')->with('success', 'Salary marked as paid.');
    }
}
