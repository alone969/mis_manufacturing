<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Process Salary</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                @if($errors->any())<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('salaries.store') }}">
                    @csrf
                    <div class="mb-4"><label class="block text-sm font-medium mb-1">Employee</label>
                        <select name="user_id" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"><option value="">Select employee...</option>@foreach($employees as $emp)<option value="{{ $emp->id }}" {{ old('user_id')==$emp->id?'selected':'' }}>{{ $emp->name }}</option>@endforeach</select></div>
                    <div class="mb-4"><label class="block text-sm font-medium mb-1">Amount ($)</label><input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-medium mb-1">Period Start</label><input type="date" name="period_start" value="{{ old('period_start') }}" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                        <div><label class="block text-sm font-medium mb-1">Period End</label><input type="date" name="period_end" value="{{ old('period_end') }}" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    </div>
                    <div class="mb-4"><label class="block text-sm font-medium mb-1">Notes</label><textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">{{ old('notes') }}</textarea></div>
                    <div class="flex gap-4"><button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Process</button><a href="{{ route('salaries.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">Cancel</a></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
