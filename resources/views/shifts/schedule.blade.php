<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Shift Schedule</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-4">
                <form method="GET" class="flex gap-4 items-end">
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Period</label>
                        <select name="period" class="rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label><input type="date" name="date" value="{{ $date }}" class="rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">View</button>
                </form>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shift</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($assignments as $a)
                        <tr>
                            <td class="px-6 py-4 text-sm">{{ $a->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $a->user->name }}</td>
                            <td class="px-6 py-4 text-sm">{{ $a->shift->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $a->shift->start_time }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $a->shift->end_time }}</td>
                            <td class="px-6 py-4 text-sm"><span class="px-2 py-1 text-xs rounded-full {{ $a->status === 'present' ? 'bg-green-100 text-green-800' : $a->status === 'late' ? 'bg-yellow-100 text-yellow-800' : $a->status === 'absent' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($a->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No assignments for this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
