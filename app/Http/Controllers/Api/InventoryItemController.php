<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of inventory items.
     */
    public function index()
    {
        $items = InventoryItem::latest()->get();

        return response()->json([
            'message' => 'Daftar item inventaris berhasil diambil.',
            'data' => $items,
        ], 200);
    }

    /**
     * Store a newly created inventory item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $item = InventoryItem::create($validated);

        return response()->json([
            'message' => 'Item inventaris berhasil ditambahkan.',
            'data' => $item,
        ], 201);
    }

    /**
     * Display the specified inventory item.
     */
    public function show($id)
    {
        $item = InventoryItem::find($id);

        if (! $item) {
            return response()->json([
                'message' => 'Item inventaris tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'message' => 'Item inventaris berhasil ditemukan.',
            'data' => $item,
        ], 200);
    }

    /**
     * Update the specified inventory item in storage.
     */
    public function update(Request $request, $id)
    {
        $item = InventoryItem::find($id);

        if (! $item) {
            return response()->json([
                'message' => 'Item inventaris tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $item->update($validated);

        return response()->json([
            'message' => 'Item inventaris berhasil diperbarui.',
            'data' => $item,
        ], 200);
    }

    /**
     * Remove the specified inventory item from storage.
     */
    public function destroy($id)
    {
        $item = InventoryItem::find($id);

        if (! $item) {
            return response()->json([
                'message' => 'Item inventaris tidak ditemukan.',
            ], 404);
        }

        $item->delete();

        return response()->json([
            'message' => 'Item inventaris berhasil dihapus.',
        ], 200);
    }
}

