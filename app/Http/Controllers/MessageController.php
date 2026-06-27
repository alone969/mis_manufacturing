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
            'sent' => Message::where('sender_id', $user->id)
                ->where('is_deleted_by_sender', false),
            default => Message::where('receiver_id', $user->id)
                ->where('is_deleted_by_receiver', false),
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

        $this->logActivity('message_send', "Sent message: {$message->subject}", Message::class, $message->id);

        return response()->json($message->load('sender:id,name', 'receiver:id,name'), 201);
    }

    /**
     * Get a single message.
     */
    public function show(Message $message): JsonResponse
    {
        $user = request()->user();

        // Check if user is sender or receiver
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            return $this->errorResponse('Unauthorized.', 403);
        }

        // Mark as read if receiver
        if ($message->receiver_id === $user->id) {
            $message->markAsRead();
        }

        return response()->json($message->load('sender:id,name', 'receiver:id,name'));
    }

    /**
     * Delete a message (soft delete for sender or receiver).
     */
    public function destroy(Message $message): JsonResponse
    {
        $user = request()->user();

        if ($message->sender_id === $user->id) {
            $message->update(['is_deleted_by_sender' => true]);
        } elseif ($message->receiver_id === $user->id) {
            $message->update(['is_deleted_by_receiver' => true]);
        } else {
            return $this->errorResponse('Unauthorized.', 403);
        }

        $this->logActivity('message_delete', "Deleted message: {$message->subject}", Message::class, $message->id);

        return response()->json([
            'message' => 'Message deleted successfully.',
        ]);
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
