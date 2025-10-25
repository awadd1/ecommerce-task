<?php

namespace Database\Seeders;

use App\Enums\WarehouseTransactionType;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Enums\UserRole;
use App\Models\WarehouseTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
 
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
        ]);

        $seller = User::factory()->create([
            'name' => 'Seller User',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::SELLER,
        ]);

        $customer = User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CUSTOMER,
        ]);

        $sellers = User::factory()->count(10)->seller()->create();
        $customers = User::factory()->count(50)->customer()->create();

        $categories = Category::factory()->count(10)->create();
        $products = Product::factory()->count(100)->create();
        Product::factory()->count(20)->inStock()->active()->create();

        foreach ($sellers->take(5) as $sellerUser) {
            Product::factory()
                ->count(5)
                ->forSeller($sellerUser)
                ->forCategory($categories->random())
                ->create();
        }

        $allSellers = $sellers->push($seller); 

        foreach (Product::all() as $product) {
            if ($product->stock > 0) {
                WarehouseTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => $allSellers->random()->id,
                    'type' => WarehouseTransactionType::INITIAL_STOCK,
                    'quantity' => $product->stock,
                    'stock_before' => 0,
                    'stock_after' => $product->stock,
                    'notes' => 'Initial stock entry',
                    'created_at' => $product->created_at,
                ]);
            }
        }

        $randomProducts = Product::inRandomOrder()->limit(30)->get();

        foreach ($randomProducts as $product) {

            $quantity = rand(10, 50);
            $stockBefore = $product->stock;
            $product->increment('stock', $quantity);

            WarehouseTransaction::create([
                'product_id' => $product->id,
                'user_id' => $allSellers->random()->id,
                'type' => WarehouseTransactionType::STOCK_IN,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $product->stock,
                'notes' => 'Stock replenishment',
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            if (rand(0, 1)) {
                $adjustQuantity = rand(5, 20);
                $stockBefore = $product->stock;
                $product->increment('stock', $adjustQuantity);

                WarehouseTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => $admin->id, 
                    'type' => WarehouseTransactionType::MANUAL_ADJUSTMENT,
                    'quantity' => $adjustQuantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $product->stock,
                    'notes' => 'Stock adjustment by admin',
                    'created_at' => now()->subDays(rand(1, 15)),
                ]);
            }

            if (rand(0, 1) && $product->stock > 5) {
                $damagedQuantity = rand(1, 5);
                $stockBefore = $product->stock;
                $product->decrement('stock', $damagedQuantity);

                WarehouseTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => $allSellers->random()->id,
                    'type' => WarehouseTransactionType::DAMAGED,
                    'quantity' => $damagedQuantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $product->stock,
                    'notes' => 'Damaged items removed from inventory',
                    'created_at' => now()->subDays(rand(1, 20)),
                ]);
            }
        }
    }
}