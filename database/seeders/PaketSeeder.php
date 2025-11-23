<?php

namespace Database\Seeders;

use App\Models\Paket;
use Illuminate\Database\Seeder;

class PaketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paket = [
            [
                'nama_paket' => 'Paket Hemat',
                'harga_bulanan' => 250000,
                'harga_pemasangan' => 100000,
                'kecepatan' => '15 MB',
                'deskripsi' => 'Paket internet hemat dengan kecepatan 15 MB, cocok untuk penggunaan sehari-hari',
                'status' => 'aktif',
            ],
            [
                'nama_paket' => 'Paket Keluarga',
                'harga_bulanan' => 300000,
                'harga_pemasangan' => 100000,
                'kecepatan' => '30 MB',
                'deskripsi' => 'Paket internet keluarga dengan kecepatan 30 MB, cocok untuk penggunaan banyak perangkat',
                'status' => 'aktif',
            ],
            [
                'nama_paket' => 'Paket Premium',
                'harga_bulanan' => 500000,
                'harga_pemasangan' => 150000,
                'kecepatan' => '50 MB',
                'deskripsi' => 'Paket internet premium dengan kecepatan 50 MB, cocok untuk kebutuhan bisnis dan gaming',
                'status' => 'aktif',
            ],
        ];

        foreach ($paket as $data) {
            Paket::create([
                'paket_id' => Paket::generatePaketId(),
                ...$data
            ]);
        }
    }
}
