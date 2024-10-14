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

it('can mark a shopping list item as bought', function () {
    // Arrange: Create an item in the database
    $item = ShoppingItem::create(['name' => 'Test Item']);
    // Act: Send a PATCH request to toggle the item's bought status
    $response = $this->patch(route('shoppingList.toggleBought', $item->id));
    // Assert: Check if the response redirects to the index page
    $response->assertRedirect(route('shoppingList.index'));
    // Assert: Check if the item's status has been updated in the database
    $this->assertEquals(1, ShoppingItem::find($item->id)->is_bought);
});

it('can delete a shopping list item', function () {
    // Arrange: Create a shopping item in the database
    $item = ShoppingItem::create([
        'name' => 'Test Item'
    ]);
    // Act: Send a DELETE request to delete the item
    $response = $this->delete(route('shoppingList.destroy', $item->id));
    // Assert: Check if the response redirects back to the shopping list page
    $response->assertRedirect(route('shoppingList.index'));
    // Assert: Check if the item was deleted from the database
    $this->assertDatabaseMissing('shopping_items', ['id' => $item->id]);
});
