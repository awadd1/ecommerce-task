<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Seller User',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::SELLER,
        ]);

        User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CUSTOMER,
        ]);

        $sellers = User::factory()->count(10)->seller()->create();
        User::factory()->count(50)->customer()->create();

        $categories = Category::factory()->count(10)->create();
        Product::factory()->count(100)->create();
        Product::factory()->count(20)->inStock()->active()->create();

        foreach ($sellers->take(5) as $seller) {
            Product::factory()
                ->count(5)
                ->forSeller($seller)
                ->forCategory($categories->random())
                ->create();
        }
    }
}
