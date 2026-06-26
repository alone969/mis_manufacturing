<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\StockItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * List all stock items.
     */
    public function index(Request $request): JsonResponse
    {
        $query = StockItem::with('updatedBy:id,name');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $items = $query->orderByDesc('updated_at')->get();

        return response()->json($items);
    }

    /**
     * Create a new stock item (admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:raw_material,finished_good',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $item = StockItem::create([
            'name' => $request->name,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'updated_by' => $request->user()->id,
        ]);

        ActivityLog::log(
            $request->user()->id,
            'stock_created',
            StockItem::class,
            $item->id,
            "Created stock item: {$item->name}",
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json($item->load('updatedBy:id,name'), 201);
    }

    /**
     * Update a stock item (admin only).
     */
    public function update(Request $request, StockItem $item): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:raw_material,finished_good',
            'quantity' => 'sometimes|numeric|min:0',
            'unit' => 'sometimes|string|max:50',
        ]);

        $item->update(array_merge(
            $request->only(['name', 'type', 'quantity', 'unit']),
            ['updated_by' => $request->user()->id],
        ));

        ActivityLog::log(
            $request->user()->id,
            'stock_updated',
            StockItem::class,
            $item->id,
            "Updated stock item: {$item->name}",
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json($item->load('updatedBy:id,name'));
    }

    /**
     * Delete a stock item (admin only).
     */
    public function destroy(Request $request, StockItem $item): JsonResponse
    {
        $name = $item->name;
        $item->delete();

        ActivityLog::log(
            $request->user()->id,
            'stock_deleted',
            StockItem::class,
            $item->id,
            "Deleted stock item: {$name}",
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json(['message' => 'Stock item deleted.']);
    }
}
