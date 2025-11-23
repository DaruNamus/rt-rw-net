<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Models\User;
use App\Models\Paket;
use App\Models\Tagihan;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user pelanggan (user_id = 2, karena admin adalah user_id = 1)
        $user = User::where('role', 'pelanggan')->first();
        
        if (!$user) {
            $this->command->error('User pelanggan tidak ditemukan. Pastikan UserSeeder sudah dijalankan terlebih dahulu.');
            return;
        }

        // Ambil paket pertama (Paket Hemat)
        $paket = Paket::where('status', 'aktif')->first();
        
        if (!$paket) {
            $this->command->error('Paket tidak ditemukan. Pastikan PaketSeeder sudah dijalankan terlebih dahulu.');
            return;
        }

        // Generate pelanggan_id
        $pelangganId = Pelanggan::generatePelangganId($user->user_id, $paket->paket_id);

        // Buat data pelanggan
        $pelanggan = Pelanggan::create([
            'pelanggan_id' => $pelangganId,
            'alamat' => 'Jl. Contoh RT 01/01 No. 123',
            'no_telepon' => '081234567890',
            'tanggal_pemasangan' => Carbon::now()->subDays(30),
            'status' => 'aktif',
        ]);

        // Buat tagihan pemasangan pertama
        $totalTagihan = $paket->harga_pemasangan + $paket->harga_bulanan;
        
        Tagihan::create([
            'tagihan_id' => Tagihan::generateTagihanId(),
            'pelanggan_id' => $pelanggan->pelanggan_id,
            'paket_id' => $paket->paket_id,
            'bulan' => Carbon::now()->month,
            'tahun' => Carbon::now()->year,
            'jumlah_tagihan' => $totalTagihan,
            'status' => 'lunas', // Tagihan pemasangan dianggap sudah lunas
            'tanggal_jatuh_tempo' => Carbon::now()->subDays(23),
            'jenis_tagihan' => 'pemasangan',
            'keterangan' => 'Tagihan pemasangan pertama',
        ]);

        $this->command->info('Pelanggan berhasil dibuat dengan pelanggan_id: ' . $pelanggan->pelanggan_id);
    }
}
