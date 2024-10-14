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
        return [
            'name' => $this->faker->word(),
            'is_bought' => $this->faker->randomElement([0, 1]),
            'sort_order' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
