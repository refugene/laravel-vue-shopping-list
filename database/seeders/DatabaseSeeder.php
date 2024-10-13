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
        User::factory()->create([
            'name' => 'Eugene Getov',
            'email' => 'e.getov@gmail.com',
        ]);

        // Seed the ShoppingItem table with 10 items
        ShoppingItem::factory(10)->create();
    }
}
