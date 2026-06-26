<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Shift;
use App\Models\ShiftAssignment;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard data based on user role.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = match ($user->role) {
            'admin' => $this->adminDashboard(),
            'manager' => $this->managerDashboard($user),
            default => $this->employeeDashboard($user),
        };

        return response()->json($data);
    }

    private function adminDashboard(): array
    {
        $today = now()->toDateString();

        return [
            'stats' => [
                'total_employees' => User::where('role', 'employee')->count(),
                'total_managers' => User::where('role', 'manager')->count(),
                'total_admins' => User::where('role', 'admin')->count(),
                'active_shifts_today' => ShiftAssignment::where('date', $today)
                    ->where('status', '!=', 'absent')
                    ->count(),
                'total_stock_items' => StockItem::sum('quantity'),
                'pending_salaries' => \App\Models\Salary::where('status', 'pending')->count(),
            ],
            'recent_activity' => ActivityLog::with('user:id,name')
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn ($log) => [
                    'id' => $log->id,
                    'user' => $log->user->name,
                    'action' => $log->action,
                    'description' => $log->description,
                    'time' => $log->created_at->diffForHumans(),
                ]),
            'today_shifts' => ShiftAssignment::with('shift:name,start_time,end_time', 'user:id,name')
                ->where('date', $today)
                ->get()
                ->map(fn ($a) => [
                    'employee' => $a->user->name,
                    'shift' => $a->shift->name,
                    'start' => $a->shift->start_time,
                    'end' => $a->shift->end_time,
                    'status' => $a->status,
                ]),
            'stock_alerts' => StockItem::where('quantity', '<', 10)
                ->get()
                ->map(fn ($item) => [
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'type' => $item->type,
                ]),
        ];
    }

    private function managerDashboard(User $user): array
    {
        $today = now()->toDateString();

        return [
            'stats' => [
                'team_members' => User::where('role', 'employee')->count(),
                'active_shifts_today' => ShiftAssignment::where('date', $today)
                    ->where('status', '!=', 'absent')
                    ->count(),
                'total_stock_items' => StockItem::sum('quantity'),
            ],
            'today_shifts' => ShiftAssignment::with('shift:name,start_time,end_time', 'user:id,name')
                ->where('date', $today)
                ->get()
                ->map(fn ($a) => [
                    'employee' => $a->user->name,
                    'shift' => $a->shift->name,
                    'start' => $a->shift->start_time,
                    'end' => $a->shift->end_time,
                    'status' => $a->status,
                ]),
            'stock_alerts' => StockItem::where('quantity', '<', 10)
                ->get()
                ->map(fn ($item) => [
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                ]),
            'recent_activity' => ActivityLog::with('user:id,name')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn ($log) => [
                    'user' => $log->user->name,
                    'action' => $log->action,
                    'description' => $log->description,
                    'time' => $log->created_at->diffForHumans(),
                ]),
        ];
    }

    private function employeeDashboard(User $user): array
    {
        $today = now()->toDateString();

        $todayAssignment = ShiftAssignment::with('shift:name,start_time,end_time')
            ->where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $recentAssignments = ShiftAssignment::with('shift:name')
            ->where('user_id', $user->id)
            ->where('date', '>=', now()->subDays(7)->toDateString())
            ->orderByDesc('date')
            ->limit(7)
            ->get()
            ->map(fn ($a) => [
                'date' => $a->date->toDateString(),
                'shift' => $a->shift->name,
                'status' => $a->status,
                'clock_in' => $a->clock_in?->format('H:i'),
                'clock_out' => $a->clock_out?->format('H:i'),
            ]);

        return [
            'stats' => [
                'today_shift' => $todayAssignment
                    ? $todayAssignment->shift->name
                    : 'No shift today',
                'attendance_this_week' => ShiftAssignment::where('user_id', $user->id)
                    ->where('date', '>=', now()->startOfWeek()->toDateString())
                    ->where('status', '!=', 'absent')
                    ->count(),
                'total_shifts_this_week' => ShiftAssignment::where('user_id', $user->id)
                    ->where('date', '>=', now()->startOfWeek()->toDateString())
                    ->count(),
            ],
            'today_shift' => $todayAssignment
                ? [
                    'shift' => $todayAssignment->shift->name,
                    'start' => $todayAssignment->shift->start_time,
                    'end' => $todayAssignment->shift->end_time,
                    'status' => $todayAssignment->status,
                    'clock_in' => $todayAssignment->clock_in?->format('H:i'),
                    'clock_out' => $todayAssignment->clock_out?->format('H:i'),
                    'assignment_id' => $todayAssignment->id,
                ]
                : null,
            'recent_attendance' => $recentAssignments,
        ];
    }
}
