<?php

namespace App\Http\Controllers;

use App\Models\DeviceLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceLogController extends Controller
{
    /**
     * List device logs for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $devices = DeviceLog::forUser($user->id)
            ->orderByDesc('last_login_at')
            ->get();

        return response()->json($devices);
    }

    /**
     * List all device logs (admin only).
     */
    public function all(Request $request): JsonResponse
    {
        $query = DeviceLog::with('user:id,name,email');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $devices = $query->orderByDesc('last_login_at')
            ->paginate($request->get('per_page', 25));

        return response()->json($devices);
    }
}
