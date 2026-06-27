<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockItem>
 */
class StockItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['raw_material', 'finished_goods', 'consumable', 'tool']),
            'quantity' => fake()->numberBetween(0, 500),
            'unit' => fake()->randomElement(['kg', 'pcs', 'liters', 'boxes', 'meters']),
            'min_stock_level' => 10,
            'cost_per_unit' => fake()->randomFloat(2, 1, 1000),
            'location' => fake()->words(2, true),
            'is_active' => true,
            'updated_by' => User::factory(),
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => 3,
        ]);
    }
}
