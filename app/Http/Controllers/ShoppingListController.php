<?php

namespace App\Http\Controllers;

use App\Models\ShoppingItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShoppingListController extends Controller
{
    public function index()
    {
        return Inertia::render('ShoppingList/Index', [
            'shoppingItems' => ShoppingItem::all(),
            'flash' => [
                'success' => session()->get('success'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:shopping_items,name',
        ]);

        $newItem = ShoppingItem::create($request->only('name'));

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

    public function destroy($id)
    {
        $item = ShoppingItem::findOrFail($id);
        $item->delete();

        return back()->with([
            'success' => 'Item deleted successfully!'
        ]);
    }

}
