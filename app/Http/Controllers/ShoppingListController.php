<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use App\Models\ShoppingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ShoppingListController extends Controller
{
    public function index()
    {
        // Allowing one shopping list for now
        $shoppingList = ShoppingList::where('user_id', auth()->id())->firstOrFail();
        $shoppingItems = $shoppingList->shoppingItems()->orderBy('sort_order', 'asc')->get();

        return Inertia::render('ShoppingList/Index', [
            'shoppingItems' => $shoppingItems,
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

        $shoppingList = ShoppingList::where('user_id', auth()->id())->firstOrFail();

        $newItem = $shoppingList->shoppingItems()->create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
        ]);

        return inertia('ShoppingList/Index', [
            'newItem' => $newItem,
            'flash' => [
                'success' => 'Item added successfully!',
            ],
        ]);
    }

    public function toggleBought($id)
    {
        $item = ShoppingItem::whereHas('shoppingList', function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($id);
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

        $shoppingList = ShoppingList::where('user_id', auth()->id())->firstOrFail();

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

        // Ensure items belong to the user's shopping list
        ShoppingItem::whereIn('id', $ids)->where('shopping_list_id', $shoppingList->id)->update([
            'sort_order' => DB::raw("(CASE " . implode(' ', $caseStatements) . " END)")
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $item = ShoppingItem::whereHas('shoppingList', function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($id);
        $item->delete();

        return back()->with([
            'success' => 'Item deleted successfully!'
        ]);
    }

}
