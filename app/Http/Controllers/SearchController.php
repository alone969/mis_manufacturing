<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Global search across employees, shifts, and stock.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
            'type' => 'nullable|in:employees,shifts,stock,all',
        ]);

        $query = $request->get('q');
        $type = $request->get('type', 'all');
        $results = [];

        if ($type === 'all' || $type === 'employees') {
            $results['employees'] = User::select('id', 'name', 'email', 'role')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                })
                ->limit(10)
                ->get();
        }

        if ($type === 'all' || $type === 'shifts') {
            $results['shifts'] = Shift::select('id', 'name', 'start_time', 'end_time')
                ->where('name', 'like', "%{$query}%")
                ->limit(10)
                ->get();
        }

        if ($type === 'all' || $type === 'stock') {
            $results['stock'] = StockItem::select('id', 'name', 'type', 'quantity', 'unit')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->limit(10)
                ->get();
        }

        return response()->json(['results' => $results]);
    }
}
