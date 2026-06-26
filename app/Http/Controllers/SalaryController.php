<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    /**
     * List all salary records (admin sees all, employees see own).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Salary::with('user:id,name,email', 'processor:id,name');

        if ($user->role === 'employee') {
            $query->where('user_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $salaries = $query->orderByDesc('period_start')->get();

        return response()->json($salaries);
    }

    /**
     * Create a salary record (admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        $salary = Salary::create([
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'status' => 'pending',
        ]);

        ActivityLog::log(
            $request->user()->id,
            'salary_created',
            Salary::class,
            $salary->id,
            "Created salary record for user #{$request->user_id}: \${$request->amount}",
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json($salary->load('user:id,name,email'), 201);
    }

    /**
     * Mark a salary as paid (admin only).
     */
    public function markPaid(Request $request, Salary $salary): JsonResponse
    {
        if ($salary->status === 'paid') {
            return response()->json(['message' => 'Salary is already paid.'], 422);
        }

        $salary->markAsPaid($request->user()->id);

        ActivityLog::log(
            $request->user()->id,
            'salary_paid',
            Salary::class,
            $salary->id,
            "Marked salary as paid for user #{$salary->user_id}: \${$salary->amount}",
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json($salary->fresh()->load('user:id,name,email', 'processor:id,name'));
    }

    /**
     * Get salary summary stats (admin only).
     */
    public function summary(): JsonResponse
    {
        return response()->json([
            'total_pending' => Salary::where('status', 'pending')->sum('amount'),
            'total_paid' => Salary::where('status', 'paid')->sum('amount'),
            'pending_count' => Salary::where('status', 'pending')->count(),
            'paid_count' => Salary::where('status', 'paid')->count(),
        ]);
    }
}
