<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productNames = [
            'Smartphone',
            'Laptop',
            'Headphones',
            'Watch',
            'Tablet',
            'T-Shirt',
            'Jeans',
            'Jacket',
            'Shoes',
            'Dress',
            'Book',
            'Notebook',
            'Pen Set',
            'Desk Lamp',
            'Chair',
            'Yoga Mat',
            'Dumbbells',
            'Basketball',
            'Running Shoes',
            'Bicycle',
            'Skincare Set',
            'Perfume',
            'Makeup Kit',
            'Hair Dryer',
            'Shaver'
        ];

        $brands = [
            'Nike',
            'Samsung',
            'Apple',
            'Sony',
            'Adidas',
            'Dell',
            'HP',
            'LG',
            'Philips',
            'Bosch'
        ];

        $name = $this->faker->randomElement($brands) . ' ' .
            $this->faker->randomElement($productNames) . ' ' .
            $this->faker->randomElement(['Pro', 'Lite', 'Max', 'Plus', '2024']);

        return [
            'name' => $name,
            'description' => $this->faker->paragraph(3),
            'price' => $this->faker->randomFloat(2, 5, 1000),
            'sku' => 'SKU-' . $this->faker->unique()->numberBetween(1000, 999999),
            'stock' => $this->faker->numberBetween(0, 500),
            'is_active' => $this->faker->boolean(85),
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function inStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'stock' => $this->faker->numberBetween(1, 500),
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn(array $attributes) => [
            'stock' => 0,
        ]);
    }

    public function priceRange(float $min, float $max): static
    {
        return $this->state(fn(array $attributes) => [
            'price' => $this->faker->randomFloat(2, $min, $max),
        ]);
    }

    public function forSeller(User $seller): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $seller->id,
        ]);
    }

    public function forCategory(Category $category): static
    {
        return $this->state(fn(array $attributes) => [
            'category_id' => $category->id,
        ]);
    }
}