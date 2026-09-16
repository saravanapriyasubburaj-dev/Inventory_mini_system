<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'           => fake()->words(2, true),
            'code'           => strtoupper(fake()->unique()->bothify('PRD-####')),
            'price_per_unit' => fake()->randomFloat(2, 10, 500),
            'tax_percentage' => fake()->randomElement([5.00, 12.00, 18.00]),
            'stock_on_hand'  => fake()->numberBetween(1, 40),
        ];
    }
}