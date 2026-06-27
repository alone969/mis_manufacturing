<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Settings</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>@endif
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf @method('PUT')
                    <h3 class="text-lg font-semibold mb-4">Notification Preferences</h3>
                    <div class="space-y-3 mb-6">
                        <label class="flex items-center gap-3"><input type="checkbox" name="settings[email_notifications]" value="1" {{ (Auth::user()->settings['email_notifications'] ?? true) ? 'checked' : '' }} class="rounded"><span class="text-sm">Email notifications</span></label>
                        <label class="flex items-center gap-3"><input type="checkbox" name="settings[shift_reminders]" value="1" {{ (Auth::user()->settings['shift_reminders'] ?? true) ? 'checked' : '' }} class="rounded"><span class="text-sm">Shift reminders</span></label>
                    </div>
                    <h3 class="text-lg font-semibold mb-4">Preferences</h3>
                    <div class="mb-4"><label class="block text-sm font-medium mb-1">Language</label>
                        <select name="settings[language]" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                            @foreach(['en' => 'English', 'es' => 'Spanish', 'fr' => 'French', 'de' => 'German', 'ar' => 'Arabic'] as $code => $name)
                            <option value="{{ $code }}" {{ (Auth::user()->settings['language'] ?? 'en') === $code ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-6"><label class="block text-sm font-medium mb-1">Theme</label>
                        <select name="settings[theme]" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                            @foreach(['light' => 'Light', 'dark' => 'Dark', 'system' => 'System'] as $val => $label)
                            <option value="{{ $val }}" {{ (Auth::user()->settings['theme'] ?? 'system') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
