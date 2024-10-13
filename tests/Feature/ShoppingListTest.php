<?php

use App\Models\ShoppingItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can add a shopping list item', function () {
    // Arrange: Create a new item data
    $itemData = ['name' => 'apple'];

    // Act: Send a POST request to add the item
    $response = $this->post(route('shoppingList.store'), $itemData);

    // Assert: Check if the response is a redirect
    $response->assertRedirect(route('shoppingList.index'));

    // Assert: Check if the item exists in the database
    $this->assertDatabaseHas('shopping_items', [
        'name' => 'apple'
    ]);
});

it('cannot add a duplicate shopping list item', function () {
    // Arrange: Create a shopping list item in the database
    ShoppingItem::create(['name' => 'apple']);

    // Act: Try to add the same item again
    $response = $this->post(route('shoppingList.store'), ['name' => 'apple']);

    // Assert: Check that the request was redirected back with an error
    $response->assertSessionHasErrors();

    // Assert: Check that only one instance of the item exists in the database
    $this->assertCount(1, ShoppingItem::where('name', 'apple')->get());
});
