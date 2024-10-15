<?php

use App\Models\User;
use App\Models\ShoppingList;
use App\Models\ShoppingItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user and authenticate before each test
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Get the default shopping list for the user
    $this->shoppingList = ShoppingList::where('user_id', $this->user->id)->firstOrFail();
});

it('can add a shopping list item with a price', function () {
    // Arrange: Create a new item data with a name and price (in pence)
    $itemData = [
        'name' => 'apple',
        'price' => 300,
    ];

    // Act: Send a POST request to add the item
    $response = $this->post(route('shoppingList.store'), $itemData);

    // Assert: Check if the response was successful (200 OK)
    $response->assertStatus(200);

    // Assert: Check if the item exists in the database with the correct name and price
    $this->assertDatabaseHas('shopping_items', [
        'name' => 'apple',
        'price' => 300,
    ]);
});

it('cannot add a duplicate shopping list item', function () {
    // Arrange: Create a shopping list item in the database
    $this->shoppingList->shoppingItems()->create([
        'name' => 'apple',
        'price' => 300
    ]);

    // Act: Try to add the same item again
    $response = $this->post(route('shoppingList.store'), ['name' => 'apple', 'price' => 300]);

    // Assert: Check that the request was redirected back with an error
    $response->assertSessionHasErrors();

    // Assert: Check that only one instance of the item exists in the database
    $this->assertCount(1, ShoppingItem::where('name', 'apple')->get());
});

it('can mark a shopping list item as bought', function () {
    // Arrange: Create an item in the database
    $item = $this->shoppingList->shoppingItems()->create([
        'name' => 'Test Item',
        'price' => 300
    ]);

    // Act: Send a PATCH request to toggle the item's bought status
    $response = $this->patch(route('shoppingList.toggleBought', $item->id));

    // Assert: Check if the response status is 302 redirect
    $response->assertStatus(302);

    // Assert: Check if the item's status has been updated in the database
    $this->assertEquals(1, ShoppingItem::find($item->id)->is_bought);
});

it('can update the sort order of shopping items', function () {
    // Arrange: Create some shopping items with initial sort orders
    $item1 = $this->shoppingList->shoppingItems()->create(['name' => 'Item 1', 'sort_order' => 0]);
    $item2 = $this->shoppingList->shoppingItems()->create(['name' => 'Item 2', 'sort_order' => 1]);
    $item3 = $this->shoppingList->shoppingItems()->create(['name' => 'Item 3', 'sort_order' => 2]);

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
    $item = $this->shoppingList->shoppingItems()->create([
        'name' => 'Test Item'
    ]);
    // Act: Send a DELETE request to delete the item
    $response = $this->delete(route('shoppingList.destroy', $item->id));

    // Assert: Check if the response status is 302
    $response->assertStatus(302);

    // Assert: Check if the item was deleted from the database
    $this->assertDatabaseMissing('shopping_items', ['id' => $item->id]);
});
