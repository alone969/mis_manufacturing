<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Create Employee</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                @if($errors->any())<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('employees.store') }}">
                    @csrf
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label><input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label><input type="password" name="password" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                        <select name="role" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="employee">Employee</option><option value="manager">Manager</option><option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="flex gap-4"><button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Create</button><a href="{{ route('employees.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">Cancel</a></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
