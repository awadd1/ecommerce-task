<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
            'name' => 'Admin ',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
        ]);

        User::create([
            'name' => 'Seller ',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::SELLER,
        ]);
      
        User::create([
            'name' => 'Customer ',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CUSTOMER,
        ]);
    }
}
