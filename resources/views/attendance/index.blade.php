<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Attendance Records</h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:underline">← Back to Dashboard</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">{{ session('error') }}</div>
            @endif

            {{-- Filters --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-4">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    @if(!Auth::user()->hasRole('employee'))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employee ID</label>
                        <input type="text" name="user_id" value="{{ request('user_id') }}" placeholder="User ID" class="rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white w-32">
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select name="status" class="rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="">All</option>
                            <option value="present" {{ request('status')=='present'?'selected':'' }}>Present</option>
                            <option value="absent" {{ request('status')=='absent'?'selected':'' }}>Absent</option>
                            <option value="late" {{ request('status')=='late'?'selected':'' }}>Late</option>
                            <option value="assigned" {{ request('status')=='assigned'?'selected':'' }}>Assigned</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">Filter</button>
                    <a href="{{ route('attendance.index') }}" class="text-gray-500 hover:text-gray-700 text-sm py-2">Clear</a>
                </form>
            </div>

            {{-- Records Table --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shift</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($records as $r)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $r->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $r->date instanceof \Carbon\Carbon ? $r->date->format('M d, Y') : $r->date }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $r->shift->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $r->clock_in ? \Carbon\Carbon::parse($r->clock_in)->format('H:i') : '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $r->clock_out ? \Carbon\Carbon::parse($r->clock_out)->format('H:i') : '-' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $colors = [
                                        'present' => 'bg-green-100 text-green-800',
                                        'late' => 'bg-yellow-100 text-yellow-800',
                                        'absent' => 'bg-red-100 text-red-800',
                                        'assigned' => 'bg-blue-100 text-blue-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full font-semibold {{ $colors[$r->status] ?? 'bg-gray-100 text-gray-800' }}">{{ ucfirst($r->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No attendance records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $records->withQueryString()->links() }}</div>
        </div>
    </div>
</x-app-layout>
