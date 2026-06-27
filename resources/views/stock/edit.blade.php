<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Edit Stock Item: {{ $item->name }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                @if($errors->any())<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('stock.update', $item) }}">
                    @csrf @method('PUT')
                    <div class="mb-4"><label class="block text-sm font-medium mb-1">Name</label><input type="text" name="name" value="{{ old('name', $item->name) }}" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    <div class="mb-4"><label class="block text-sm font-medium mb-1">Type</label><select name="type" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"><option value="raw_material" {{ $item->type==='raw_material'?'selected':'' }}>Raw Material</option><option value="finished_good" {{ $item->type==='finished_good'?'selected':'' }}>Finished Good</option></select></div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-medium mb-1">Quantity</label><input type="number" name="quantity" value="{{ old('quantity', $item->quantity) }}" step="0.01" min="0" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                        <div><label class="block text-sm font-medium mb-1">Unit</label><input type="text" name="unit" value="{{ old('unit', $item->unit) }}" required class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-medium mb-1">Min Quantity</label><input type="number" name="minimum_quantity" value="{{ old('minimum_quantity', $item->minimum_quantity) }}" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                        <div><label class="block text-sm font-medium mb-1">SKU</label><input type="text" name="sku" value="{{ old('sku', $item->sku) }}" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"></div>
                    </div>
                    <div class="mb-4"><label class="block text-sm font-medium mb-1">Description</label><textarea name="description" rows="3" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">{{ old('description', $item->description) }}</textarea></div>
                    <div class="flex gap-4"><button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Update</button><a href="{{ route('stock.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">Cancel</a></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
