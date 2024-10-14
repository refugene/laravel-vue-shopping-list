<?php

namespace App\Http\Controllers;

use App\Models\ShoppingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ShoppingListController extends Controller
{
    public function index()
    {
        return Inertia::render('ShoppingList/Index', [
            'shoppingItems' => ShoppingItem::orderBy('sort_order', 'asc')->get(),
            'flash' => [
                'success' => session()->get('success'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:shopping_items,name',
            'price' => 'required|integer|min:0'
        ]);

        $newItem = ShoppingItem::create($request->only('name', 'price'));

        return inertia('ShoppingList/Index', [
            'newItem' => $newItem,
            'flash' => [
                'success' => 'Item added successfully!',
            ],
        ]);
    }

    public function toggleBought($id)
    {
        $item = ShoppingItem::findOrFail($id);
        $item->is_bought = !$item->is_bought;
        $item->save();

        return back()->with([
            'success' => 'Item updated successfully!',
        ]);
    }

    public function updateSortOrder(Request $request)
    {
        // Validate the orderedItems input
        $request->validate([
            'orderedItems' => 'required|array',
            'orderedItems.*' => 'integer|min:0',
        ]);

        // Array of item IDs in new order
        $orderedItems = $request->input('orderedItems');

        // Building one SQL query for efficiency, and cast all IDs and Indexes to (int) to prevent sql injection
        $caseStatements = [];
        $ids = [];

        foreach ($orderedItems as $index => $itemId) {
            $itemId = (int) $itemId;
            $index = (int) $index;

            $caseStatements[] = "WHEN id = {$itemId} THEN {$index}";
            $ids[] = $itemId;
        }

        $caseSql = implode(' ', $caseStatements);
        DB::update("UPDATE shopping_items SET sort_order = CASE {$caseSql} END WHERE id IN (" . implode(',', $ids) . ")");

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $item = ShoppingItem::findOrFail($id);
        $item->delete();

        return back()->with([
            'success' => 'Item deleted successfully!'
        ]);
    }

}
