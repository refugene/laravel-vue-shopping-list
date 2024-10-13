<?php

namespace App\Http\Controllers;

use App\Models\ShoppingItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShoppingListController extends Controller
{
    public function index()
    {
        $shoppingItems = ShoppingItem::all();

        // Return an Inertia response for Vue component
        return Inertia::render('ShoppingList/Index', [
            'shoppingItems' => $shoppingItems
        ]);

    }
}
