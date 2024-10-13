<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShoppingItem>
 */
class ShoppingItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shoppingItems = [
            'apple', 'banana', 'carrot', 'milk', 'bread', 'tomato', 'chicken', 'rice', 'eggs', 'butter'
        ];

        return [
            'name' => $this->faker->randomElement($shoppingItems),
        ];
    }
}
