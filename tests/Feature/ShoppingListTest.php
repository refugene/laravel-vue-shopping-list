<?php

use App\Models\ShoppingItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can add a shopping list item', function () {
    // Arrange: Create a new item data
    $itemData = ['name' => 'apple'];

    // Act: Send a POST request to add the item
    $response = $this->post(route('shoppingList.store'), $itemData);

    // Assert: Check if the response was successful (200 OK)
    $response->assertStatus(200);

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

    // Assert: Check if the response status is 302 redirect
    $response->assertStatus(302);

    // Assert: Check if the item's status has been updated in the database
    $this->assertEquals(1, ShoppingItem::find($item->id)->is_bought);
});

it('can update the sort order of shopping items', function () {
    // Arrange: Create some shopping items with initial sort orders
    $item1 = ShoppingItem::create(['name' => 'Item 1', 'sort_order' => 0]);
    $item2 = ShoppingItem::create(['name' => 'Item 2', 'sort_order' => 1]);
    $item3 = ShoppingItem::create(['name' => 'Item 3', 'sort_order' => 2]);

    // Act: Define the new order of items (reverse the order in this case)
    $newOrder = [$item3->id, $item1->id, $item2->id];

    // Send a PATCH request to update the sort order
    $response = $this->patch(route('shoppingList.updateSortOrder'), [
        'orderedItems' => $newOrder,
    ]);

    // Assert: Check if the response was successful
    $response->assertStatus(200);

    // Assert: Check if the items in the database have the correct sort order
    $this->assertDatabaseHas('shopping_items', [
        'id' => $item3->id,
        'sort_order' => 0, // Item 3 should now be first
    ]);

    $this->assertDatabaseHas('shopping_items', [
        'id' => $item1->id,
        'sort_order' => 1, // Item 1 should now be second
    ]);

    $this->assertDatabaseHas('shopping_items', [
        'id' => $item2->id,
        'sort_order' => 2, // Item 2 should now be third
    ]);
});

it('can delete a shopping list item', function () {
    // Arrange: Create a shopping item in the database
    $item = ShoppingItem::create([
        'name' => 'Test Item'
    ]);
    // Act: Send a DELETE request to delete the item
    $response = $this->delete(route('shoppingList.destroy', $item->id));

    // Assert: Check if the response status is 302
    $response->assertStatus(302);

    // Assert: Check if the item was deleted from the database
    $this->assertDatabaseMissing('shopping_items', ['id' => $item->id]);
});
