<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Assign Employees to {{ $shift->name }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>@endif
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="POST" action="{{ route('shifts.assign', $shift) }}">
                    @csrf
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label><input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Employees</label>
                        <div class="grid grid-cols-2 gap-2 max-h-64 overflow-y-auto border rounded-md p-3">
                            @foreach($employees as $emp)
                            <label class="flex items-center gap-2"><input type="checkbox" name="user_ids[]" value="{{ $emp->id }}" class="rounded"><span class="text-sm">{{ $emp->name }} ({{ $emp->email }})</span></label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-4"><button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Assign</button><a href="{{ route('shifts.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">Cancel</a></div>
                </form>
            </div>
            @if($existingAssignments->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Current Assignments</h3>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead><tr><th class="px-4 py-2 text-left text-xs text-gray-500">Employee</th><th class="px-4 py-2 text-left text-xs text-gray-500">Date</th><th class="px-4 py-2 text-left text-xs text-gray-500">Status</th><th class="px-4 py-2 text-left text-xs text-gray-500">Action</th></tr></thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($existingAssignments as $a)
                        <tr>
                            <td class="px-4 py-2 text-sm">{{ $a->user->name }}</td>
                            <td class="px-4 py-2 text-sm">{{ $a->date->format('M d, Y') }}</td>
                            <td class="px-4 py-2 text-sm"><span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ ucfirst($a->status) }}</span></td>
                            <td class="px-4 py-2 text-sm"><form method="POST" action="{{ route('shifts.unassign', $a) }}" class="inline" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Remove</button></form></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
