<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Activity Logs</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-4">
                <form method="GET" class="flex gap-4">
                    <input type="text" name="user_id" value="{{ request('user_id') }}" placeholder="User ID" class="rounded-md border-gray-300 dark:bg-gray-700 dark:text-white w-32">
                    <select name="action" class="rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                        <option value="">All Actions</option>
                        <option value="login" {{ request('action')=='login'?'selected':'' }}>Login</option>
                        <option value="logout" {{ request('action')=='logout'?'selected':'' }}>Logout</option>
                        <option value="create" {{ request('action')=='create'?'selected':'' }}>Create</option>
                        <option value="update" {{ request('action')=='update'?'selected':'' }}>Update</option>
                        <option value="delete" {{ request('action')=='delete'?'selected':'' }}>Delete</option>
                        <option value="clock_in" {{ request('action')=='clock_in'?'selected':'' }}>Clock In</option>
                        <option value="clock_out" {{ request('action')=='clock_out'?'selected':'' }}>Clock Out</option>
                    </select>
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Filter</button>
                </form>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($logs as $log)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $log->created_at->format('M d, H:i') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $log->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm"><span class="px-2 py-1 text-xs rounded-full {{ match($log->action) { 'login','logout' => 'bg-blue-100 text-blue-800', 'create' => 'bg-green-100 text-green-800', 'update' => 'bg-yellow-100 text-yellow-800', 'delete' => 'bg-red-100 text-red-800', default => 'bg-gray-100 text-gray-800' } }}">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span></td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $log->description }}</td>
                            <td class="px-6 py-4 text-sm text-gray-400">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $logs->withQueryString()->links() }}</div>
        </div>
    </div>
</x-app-layout>
