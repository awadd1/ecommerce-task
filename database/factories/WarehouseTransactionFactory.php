<?php

namespace Database\Factories;

use App\Enums\WarehouseTransactionType;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WarehouseTransaction>
 */
class WarehouseTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stockBefore = $this->faker->numberBetween(0, 500);
        $quantity = $this->faker->numberBetween(1, 50);
        $type = $this->faker->randomElement(WarehouseTransactionType::cases());

        $stockAfter = $type->isDeduction()
            ? max(0, $stockBefore - $quantity)
            : $stockBefore + $quantity;

        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'type' => $type,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}