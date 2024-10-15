<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShoppingItem;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->createMany([
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ],
            [
                'name' => 'Test User 2',
                'email' => 'test2@example.com',
            ],
        ]);

        // Seed the ShoppingItem table with 10 items
        $shoppingItems = [
            'apple', 'banana', 'carrot', 'milk', 'bread', 'tomato', 'chicken', 'rice', 'eggs', 'butter'
        ];
        foreach ($shoppingItems as $item) {
            ShoppingItem::factory()->create([
                // Override the 'name' from the factory
                'name' => $item,
            ]);
        }
    }
}
