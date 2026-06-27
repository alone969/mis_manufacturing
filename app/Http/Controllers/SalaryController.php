<?php

namespace App\Http\Controllers;

use App\Models\Notification;
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

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $salaries = $query->orderByDesc('period_start')->get();

        return response()->json($salaries);
    }

    /**
     * Process salary for an employee (admin).
     */
    public function process(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if salary already exists for this period
        $existing = Salary::where('user_id', $request->user_id)
            ->where('period_start', $request->period_start)
            ->where('period_end', $request->period_end)
            ->first();

        if ($existing) {
            return $this->errorResponse('Salary already processed for this period.', 422);
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

        $this->logActivity('salary_process', "Processed salary for user ID: {$request->user_id}", Salary::class, $salary->id);

        // Create notification for the employee
        Notification::create([
            'user_id' => $request->user_id,
            'type' => 'salary_processed',
            'title' => 'Salary Processed',
            'message' => 'Your salary for ' . $request->period_start . ' to ' . $request->period_end . ' has been processed.',
            'data' => ['salary_id' => $salary->id, 'amount' => $request->amount],
        ]);

        return response()->json([
            'message' => 'Salary processed successfully.',
            'salary' => $salary->load('user:id,name,email'),
        ], 201);
    }

    /**
     * Mark salary as paid (admin).
     */
    public function pay(Request $request, Salary $salary): JsonResponse
    {
        $result = $salary->markAsPaid();

        if (! $result) {
            return $this->errorResponse('Salary has already been paid or failed.', 422);
        }

        $this->logActivity('salary_pay', "Marked salary as paid for user ID: {$salary->user_id}", Salary::class, $salary->id);

        return response()->json([
            'message' => 'Salary marked as paid.',
            'salary' => $salary->fresh()->load('user:id,name,email'),
        ]);
    }
}
