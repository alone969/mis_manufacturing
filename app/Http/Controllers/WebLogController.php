<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DeviceLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebLogController extends Controller
{
    /**
     * List activity logs (admin).
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user:id,name,email');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->latest()->paginate(25);

        return view('logs.activity', compact('logs'));
    }

    /**
     * List device logs (admin).
     */
    public function deviceLogs(Request $request): View
    {
        $query = DeviceLog::with('user:id,name,email');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $devices = $query->orderByDesc('last_login_at')->paginate(25);

        return view('logs.devices', compact('devices'));
    }
}
