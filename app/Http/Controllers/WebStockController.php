<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebStockController extends Controller
{
    /**
     * List stock items.
     */
    public function index(Request $request): View
    {
        $query = StockItem::with('updater:id,name');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('low_stock') && $request->low_stock === 'true') {
            $query->lowStock();
        }
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')->paginate(20);

        return view('stock.index', compact('items'));
    }

    /**
     * Show create stock item form (admin).
     */
    public function create(): View
    {
        return view('stock.create');
    }

    /**
     * Store stock item (admin).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:raw_material,finished_good',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'minimum_quantity' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'sku' => 'nullable|string|unique:stock_items,sku',
        ]);

        $item = StockItem::create([
            ...$request->only(['name', 'type', 'quantity', 'unit', 'minimum_quantity', 'description', 'sku']),
            'updated_by' => $request->user()->id,
        ]);

        $this->logActivity('create', "Created stock item: {$item->name}", StockItem::class, $item->id);

        return redirect()->route('stock.index')->with('success', 'Stock item created successfully.');
    }

    /**
     * Show edit stock item form (admin).
     */
    public function edit(StockItem $item): View
    {
        return view('stock.edit', compact('item'));
    }

    /**
     * Update stock item (admin).
     */
    public function update(Request $request, StockItem $item)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:raw_material,finished_good',
            'quantity' => 'sometimes|numeric|min:0',
            'unit' => 'sometimes|string|max:50',
            'minimum_quantity' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'sku' => 'sometimes|string|unique:stock_items,sku,' . $item->id,
        ]);

        $item->update(array_merge(
            $request->only(['name', 'type', 'quantity', 'unit', 'minimum_quantity', 'description', 'sku']),
            ['updated_by' => $request->user()->id]
        ));

        $this->logActivity('update', "Updated stock item: {$item->name}", StockItem::class, $item->id);

        return redirect()->route('stock.index')->with('success', 'Stock item updated successfully.');
    }

    /**
     * Delete stock item (admin).
     */
    public function destroy(StockItem $item)
    {
        $itemName = $item->name;
        $item->delete();
        $this->logActivity('delete', "Deleted stock item: {$itemName}", StockItem::class, $item->id);

        return redirect()->route('stock.index')->with('success', 'Stock item deleted successfully.');
    }
}
