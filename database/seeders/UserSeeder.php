<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'user_id' => User::generateUserId(),
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Pelanggan User
        User::create([
            'user_id' => User::generateUserId(),
            'name' => 'Pelanggan Demo',
            'email' => 'pelanggan@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pelanggan',
        ]);
    }
}
