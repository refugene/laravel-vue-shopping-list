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

        ShoppingItem::create($request->only('name'));

        return redirect()->route('shoppingList.index')->with('success', 'Item added successfully!');
    }

    public function destroy($id)
    {
        $item = ShoppingItem::findOrFail($id);
        $item->delete();
        return redirect()->route('shoppingList.index')->with('success', 'Item deleted successfully!');
    }

}
