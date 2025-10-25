<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Electronics',
            'Clothing',
            'Home & Kitchen',
            'Books',
            'Sports & Outdoors',
            'Beauty & Personal Care',
            'Toys & Games',
            'Automotive',
            'Health & Fitness',
            'Jewelry & Accessories',
            'Computers & Laptops',
            'Smartphones & Tablets',
            'Home Appliances',
            'Fashion & Apparel',
            'Shoes & Footwear',
            'Watches & Accessories',
            'Furniture & Decor',
            'Garden & Outdoor',
            'Tools & Hardware',
            'Pet Supplies',
            'Baby & Kids',
            'Groceries & Food',
            'Office Supplies',
            'Musical Instruments',
            'Art & Craft',
            'Camera & Photo',
            'Video Games',
            'Music & Movies',
            'Travel & Luggage',
            'Industrial & Scientific',
            'Software',
            'Fitness Equipment',
            'Outdoor Gear',
            'Kitchenware',
            'Bed & Bath',
            'Lighting',
            'Storage & Organization',
            'Party Supplies',
            'School Supplies',
            'Craft Supplies'
        ];

        return [
            'name' => $this->faker->randomElement($categories),
            'description' => $this->faker->paragraph(2),
            'is_active' => $this->faker->boolean(90),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
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
}
