<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ $message->subject }}</h2>
            <a href="{{ route('messages.index', ['box' => $box ?? 'inbox']) }}" class="text-blue-600 hover:underline text-sm">← Back to {{ ucfirst($box ?? 'inbox') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6 pb-4 border-b">
                    <div>
                        <p class="text-sm text-gray-500">From: <span class="font-medium text-gray-900 dark:text-white">{{ $message->sender->name }}</span> ({{ $message->sender->email }})</p>
                        <p class="text-sm text-gray-500">To: <span class="font-medium text-gray-900 dark:text-white">{{ $message->receiver->name }}</span> ({{ $message->receiver->email }})</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $message->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="prose dark:prose-invert max-w-none text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $message->body }}</div>
                <div class="mt-6 pt-4 border-t flex justify-between">
                    <a href="{{ route('messages.create') }}" class="text-blue-600 hover:underline text-sm">↩ Reply</a>
                    <form method="POST" action="{{ route('messages.destroy', $message) }}" onsubmit="return confirm('Delete this message?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline text-sm">Delete</button></form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
