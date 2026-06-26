<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * List messages for the authenticated user (inbox + sent).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $box = $request->get('box', 'inbox');

        $query = match ($box) {
            'sent' => Message::where('sender_id', $user->id),
            default => Message::where('receiver_id', $user->id),
        };

        $messages = $query->with('sender:id,name', 'receiver:id,name')
            ->latest()
            ->paginate(20);

        return response()->json($messages);
    }

    /**
     * Send a message.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'body' => $request->body,
        ]);

        return response()->json($message->load('sender:id,name', 'receiver:id,name'), 201);
    }

    /**
     * Mark a message as read.
     */
    public function markRead(Message $message): JsonResponse
    {
        $message->markAsRead();

        return response()->json($message->fresh());
    }

    /**
     * Get unread message count.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Message::where('receiver_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}
