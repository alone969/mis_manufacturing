<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Notifications</h2>
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">@csrf @method('PUT')<button class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Mark All Read</button></form>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>@endif
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($notifications as $n)
                    <div class="px-6 py-4 {{ !$n->is_read ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $n->title }}</p>
                                <p class="text-sm text-gray-500 mt-1">{{ $n->message }}</p>
                                <span class="text-xs px-2 py-1 rounded-full mt-2 inline-block {{ $n->type === 'stock_alert' ? 'bg-red-100 text-red-800' : $n->type === 'shift_assigned' ? 'bg-blue-100 text-blue-800' : $n->type === 'salary_processed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ str_replace('_', ' ', ucfirst($n->type)) }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-400">{{ $n->created_at->diffForHumans() }}</span>
                                @if(!$n->is_read)
                                <form method="POST" action="{{ route('notifications.mark-read', $n) }}" class="mt-1">@csrf @method('PUT')<button class="text-blue-600 hover:underline text-xs">Mark Read</button></form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">No notifications.</div>
                    @endforelse
                </div>
            </div>
            <div class="mt-4">{{ $notifications->links() }}</div>
        </div>
    </div>
</x-app-layout>
