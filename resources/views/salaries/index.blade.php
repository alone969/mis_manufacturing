<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Salary Management</h2>
            <a href="{{ route('salaries.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">+ Process Salary</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>@endif
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Processed By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($salaries as $salary)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $salary->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold">${{ number_format($salary->amount, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $salary->period_start->format('M d') }} - {{ $salary->period_end->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm"><span class="px-2 py-1 text-xs rounded-full {{ $salary->status === 'paid' ? 'bg-green-100 text-green-800' : $salary->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($salary->status) }}</span></td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $salary->processor->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($salary->status === 'pending')
                                <form method="POST" action="{{ route('salaries.pay', $salary) }}" class="inline">@csrf @method('PATCH')<button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">Mark Paid</button></form>
                                @else <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No salary records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $salaries->links() }}</div>
        </div>
    </div>
</x-app-layout>
