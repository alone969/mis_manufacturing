<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Messages</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>@endif
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-4">
                <div class="flex justify-between items-center">
                    <div class="flex gap-4">
                        <a href="{{ route('messages.index', ['box' => 'inbox']) }}" class="px-4 py-2 rounded-lg text-sm {{ $box==='inbox' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">Inbox @if($unreadCount > 0)<span class="ml-1 bg-red-500 text-white text-xs rounded-full px-2">{{ $unreadCount }}</span>@endif</a>
                        <a href="{{ route('messages.index', ['box' => 'sent']) }}" class="px-4 py-2 rounded-lg text-sm {{ $box==='sent' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">Sent</a>
                    </div>
                    <a href="{{ route('messages.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">+ Compose</a>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($messages as $msg)
                    <a href="{{ route('messages.show', $msg) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 px-6 py-4 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white {{ !$msg->read_at && $box==='inbox' ? 'font-bold' : '' }}">{{ $msg->subject }}</p>
                                <p class="text-sm text-gray-500 mt-1">From: {{ $msg->sender->name }} → To: {{ $msg->receiver->name }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $msg->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">No messages found.</div>
                    @endforelse
                </div>
            </div>
            <div class="mt-4">{{ $messages->withQueryString()->links() }}</div>
        </div>
    </div>
</x-app-layout>
