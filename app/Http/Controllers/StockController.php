<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * List all stock items.
     */
    public function index(Request $request): JsonResponse
    {
        $query = StockItem::with('updater:id,name');

        // Type filter
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Low stock filter
        if ($request->has('low_stock') && $request->low_stock === 'true') {
            $query->lowStock();
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('name')
            ->paginate($request->get('per_page', 20));

        return response()->json($items);
    }

    /**
     * Create a new stock item (admin).
     */
    public function store(Request $request): JsonResponse
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

        // Check for low stock alert and notify admin/manager users
        if ($item->isLowStock()) {
            $this->logActivity('stock_alert', "Low stock alert for: {$item->name}", StockItem::class, $item->id);

            // Notify admin and manager users
            $adminsManagers = User::whereIn('role', ['admin', 'manager'])->get();
            foreach ($adminsManagers as $adminUser) {
                Notification::create([
                    'user_id' => $adminUser->id,
                    'type' => 'stock_alert',
                    'title' => 'Low Stock Alert',
                    'message' => "{$item->name} is below minimum stock level ({$item->quantity} {$item->unit}).",
                    'data' => ['stock_item_id' => $item->id],
                ]);
            }
        }

        return response()->json([
            'message' => 'Stock item created successfully.',
            'item' => $item,
        ], 201);
    }

    /**
     * Update a stock item (admin).
     */
    public function update(Request $request, StockItem $item): JsonResponse
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

        return response()->json([
            'message' => 'Stock item updated successfully.',
            'item' => $item,
        ]);
    }

    /**
     * Delete a stock item (admin).
     */
    public function destroy(StockItem $item): JsonResponse
    {
        $itemName = $item->name;
        $item->delete();

        $this->logActivity('delete', "Deleted stock item: {$itemName}", StockItem::class, $item->id);

        return response()->json([
            'message' => 'Stock item deleted successfully.',
        ]);
    }
}
