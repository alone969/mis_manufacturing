<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Edit Shift: {{ $shift->name }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                @if($errors->any())<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('shifts.update', $shift) }}">
                    @csrf @method('PUT')
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Shift Name</label><input type="text" name="name" value="{{ old('name', $shift->name) }}" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Time</label><input type="time" name="start_time" value="{{ old('start_time', $shift->start_time) }}" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Time</label><input type="time" name="end_time" value="{{ old('end_time', $shift->end_time) }}" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    <div class="flex gap-4"><button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Update</button><a href="{{ route('shifts.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">Cancel</a></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
