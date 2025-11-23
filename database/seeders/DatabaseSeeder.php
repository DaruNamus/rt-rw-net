<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PaketSeeder::class,      // Seed paket internet terlebih dahulu
            UserSeeder::class,        // Seed user (admin & pelanggan)
            PelangganSeeder::class,   // Seed pelanggan (harus setelah UserSeeder)
        ]);
    }
}
