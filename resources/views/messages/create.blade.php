<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Compose Message</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                @if($errors->any())<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('messages.store') }}">
                    @csrf
                    <div class="mb-4"><label class="block text-sm font-medium mb-1">To</label>
                        <select name="receiver_id" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"><option value="">Select recipient...</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('receiver_id')==$u->id?'selected':'' }}>{{ $u->name }} ({{ $u->email }})</option>@endforeach</select></div>
                    <div class="mb-4"><label class="block text-sm font-medium mb-1">Subject</label><input type="text" name="subject" value="{{ old('subject') }}" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    <div class="mb-4"><label class="block text-sm font-medium mb-1">Message</label><textarea name="body" rows="6" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">{{ old('body') }}</textarea></div>
                    <div class="flex gap-4"><button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Send</button><a href="{{ route('messages.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">Cancel</a></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
