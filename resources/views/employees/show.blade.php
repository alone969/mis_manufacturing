<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ $user->name }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Profile Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div><span class="text-gray-500 text-sm">Name:</span><p class="font-medium">{{ $user->name }}</p></div>
                    <div><span class="text-gray-500 text-sm">Email:</span><p class="font-medium">{{ $user->email }}</p></div>
                    <div><span class="text-gray-500 text-sm">Role:</span><p class="font-medium"><span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : $user->role === 'manager' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">{{ ucfirst($user->role) }}</span></p></div>
                    <div><span class="text-gray-500 text-sm">Status:</span><p class="font-medium"><span class="px-2 py-1 text-xs rounded-full {{ $user->onboarding_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($user->onboarding_status) }}</span></p></div>
                    <div><span class="text-gray-500 text-sm">Joined:</span><p class="font-medium">{{ $user->created_at->format('M d, Y') }}</p></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Recent Attendance</h3>
                @if($user->shiftAssignments->count() > 0)
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead><tr><th class="px-4 py-2 text-left text-xs text-gray-500">Date</th><th class="px-4 py-2 text-left text-xs text-gray-500">Shift</th><th class="px-4 py-2 text-left text-xs text-gray-500">Clock In</th><th class="px-4 py-2 text-left text-xs text-gray-500">Clock Out</th><th class="px-4 py-2 text-left text-xs text-gray-500">Status</th></tr></thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($user->shiftAssignments->take(10) as $a)
                    <tr>
                        <td class="px-4 py-2 text-sm">{{ $a->date->format('M d, Y') }}</td>
                        <td class="px-4 py-2 text-sm">{{ $a->shift->name }}</td>
                        <td class="px-4 py-2 text-sm">{{ $a->clock_in?->format('H:i') ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm">{{ $a->clock_out?->format('H:i') ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm"><span class="px-2 py-1 text-xs rounded-full {{ $a->status === 'present' ? 'bg-green-100 text-green-800' : $a->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($a->status) }}</span></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                @else <p class="text-gray-500">No attendance records yet.</p>@endif
            </div>
        </div>
    </div>
</x-app-layout>
