<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraphs(2, true),
            'price' => fake()->randomFloat(2, 5, 500),
            'points' => fake()->numberBetween(10, 1000),
            'category_id' => Category::factory(),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }

    /**
     * Indicate that the product is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Set a specific category for the product.
     */
    public function forCategory(Category $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category->id,
        ]);
    }

    /**
     * Create a product with low price.
     */
    public function lowPrice(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 5, 50),
            'points' => fake()->numberBetween(10, 100),
        ]);
    }

    /**
     * Create a product with high price.
     */
    public function highPrice(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 100, 500),
            'points' => fake()->numberBetween(500, 1000),
        ]);
    }
}