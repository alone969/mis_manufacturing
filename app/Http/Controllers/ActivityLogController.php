<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * List activity logs (admin only).
     */
    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::with('user:id,name,email');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->latest()
            ->paginate($request->get('per_page', 25));

        return response()->json($logs);
    }
}
