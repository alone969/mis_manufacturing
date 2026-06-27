<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Shifts</h2>
            <div class="flex gap-2">
                <a href="{{ route('shifts.schedule') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Schedule</a>
                <a href="{{ route('shifts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">+ Add Shift</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>@endif
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($shifts as $shift)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $shift->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $shift->start_time }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $shift->end_time }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $shift->creator->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $shift->assignments->count() }} employees</td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('shifts.assign-form', $shift) }}" class="text-green-600 hover:underline">Assign</a>
                                <a href="{{ route('shifts.edit', $shift) }}" class="text-yellow-600 hover:underline">Edit</a>
                                @if(Auth::user()->hasRole('admin'))
                                <form method="POST" action="{{ route('shifts.destroy', $shift) }}" class="inline" onsubmit="return confirm('Delete this shift?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:underline">Delete</button></form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No shifts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
