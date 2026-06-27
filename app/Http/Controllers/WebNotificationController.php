<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebNotificationController extends Controller
{
    /**
     * List notifications.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Notification::where('user_id', $user->id);

        if ($request->has('unread_only') && $request->unread_only === 'true') {
            $query->where('is_read', false);
        }

        $notifications = $query->orderByDesc('created_at')->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read.
     */
    public function markRead(Notification $notification)
    {
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
