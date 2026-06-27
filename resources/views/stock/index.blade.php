<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Stock Management</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>@endif
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-4">
                <div class="flex justify-between items-center">
                    <form method="GET" class="flex gap-4">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search stock..." class="rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                        <select name="type" class="rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                            <option value="">All Types</option><option value="raw_material" {{ request('type')=='raw_material'?'selected':'' }}>Raw Material</option><option value="finished_good" {{ request('type')=='finished_good'?'selected':'' }}>Finished Good</option>
                        </select>
                        <label class="flex items-center gap-1 text-sm"><input type="checkbox" name="low_stock" value="true" {{ request('low_stock')=='true'?'checked':'' }}> Low Stock</label>
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">Filter</button>
                    </form>
                    @if(Auth::user()->hasRole('admin'))<a href="{{ route('stock.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">+ Add Item</a>@endif
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        @if(Auth::user()->hasRole('admin'))<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>@endif
                    </tr></thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($items as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->sku ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm"><span class="px-2 py-1 text-xs rounded-full {{ $item->type === 'raw_material' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">{{ str_replace('_', ' ', ucfirst($item->type)) }}</span></td>
                            <td class="px-6 py-4 text-sm font-semibold {{ $item->isLowStock() ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->unit }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->minimum_quantity }}</td>
                            <td class="px-6 py-4 text-sm">@if($item->isLowStock())<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Low Stock</span>@else<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">OK</span>@endif</td>
                            @if(Auth::user()->hasRole('admin'))<td class="px-6 py-4 text-sm space-x-2"><a href="{{ route('stock.edit', $item) }}" class="text-yellow-600 hover:underline">Edit</a><form method="POST" action="{{ route('stock.destroy', $item) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Delete</button></form></td>@endif
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">No stock items found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $items->withQueryString()->links() }}</div>
        </div>
    </div>
</x-app-layout>
