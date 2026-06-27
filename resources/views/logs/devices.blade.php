<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Device Logs</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Browser</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">OS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Login</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($devices as $d)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $d->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm">{{ $d->browser ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">{{ $d->operating_system ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $d->ip_address ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $d->last_login_at?->format('M d, H:i') ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No device logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $devices->links() }}</div>
        </div>
    </div>
</x-app-layout>
