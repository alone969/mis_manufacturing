<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebMessageController extends Controller
{
    /**
     * List messages (inbox).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $box = $request->get('box', 'inbox');

        $query = match ($box) {
            'sent' => Message::where('sender_id', $user->id)->where('is_deleted_by_sender', false),
            default => Message::where('receiver_id', $user->id)->where('is_deleted_by_receiver', false),
        };

        $messages = $query->with('sender:id,name', 'receiver:id,name')
            ->latest()->paginate(20);

        $unreadCount = Message::where('receiver_id', $user->id)->whereNull('read_at')->count();

        return view('messages.index', compact('messages', 'box', 'unreadCount'));
    }

    /**
     * Show create message form.
     */
    public function create(): View
    {
        $users = User::where('id', '!=', request()->user()->id)->orderBy('name')->get();

        return view('messages.create', compact('users'));
    }

    /**
     * Store message.
     */
    public function store(Request $request)
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

        return redirect()->route('messages.index', ['box' => 'sent'])->with('success', 'Message sent successfully.');
    }

    /**
     * View a single message.
     */
    public function show(Message $message): View
    {
        $user = request()->user();

        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403);
        }

        if ($message->receiver_id === $user->id) {
            $message->markAsRead();
        }

        $message->load('sender:id,name,email', 'receiver:id,name,email');

        $box = $message->sender_id === $user->id ? 'sent' : 'inbox';

        return view('messages.show', compact('message', 'box'));
    }

    /**
     * Delete message (soft delete).
     */
    public function destroy(Message $message)
    {
        $user = request()->user();

        if ($message->sender_id === $user->id) {
            $message->update(['is_deleted_by_sender' => true]);
        } elseif ($message->receiver_id === $user->id) {
            $message->update(['is_deleted_by_receiver' => true]);
        }

        $this->logActivity('message_delete', "Deleted message: {$message->subject}", Message::class, $message->id);

        return redirect()->route('messages.index')->with('success', 'Message deleted successfully.');
    }
}
