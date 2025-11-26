<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paket extends Model
{
    protected $table = 'paket';

    protected $primaryKey = 'paket_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'paket_id',
        'nama_paket',
        'harga_bulanan',
        'harga_pemasangan',
        'kecepatan',
        'deskripsi',
        'status',
    ];

    /**
     * Generate paket_id baru
     * Format: PKT1, PKT2, PKT3, ...
     */
    public static function generatePaketId(): string
    {
        // Ambil paket_id terakhir berdasarkan nilai numeriknya
        $lastPaket = static::orderByRaw('CAST(SUBSTRING(paket_id, 4) AS UNSIGNED) DESC')->first();
        
        if ($lastPaket && preg_match('/PKT(\d+)/', $lastPaket->paket_id, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'PKT' . $nextNumber;
    }

    protected function casts(): array
    {
        return [
            'harga_bulanan' => 'decimal:2',
            'harga_pemasangan' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke Pelanggan
     * Note: Tidak ada foreign key langsung karena paket_id disembunyikan di pelanggan_id
     * Gunakan method getPelanggan() untuk mendapatkan pelanggan yang menggunakan paket ini
     */
    public function getPelanggan()
    {
        // Cari pelanggan yang memiliki paket_id ini di pelanggan_id
        $paketId = $this->paket_id;
        
        // Jika format baru (PKT1, PKT2, dst)
        if (preg_match('/PKT(\d+)/', $paketId, $matches)) {
            $numericPaketId = (int) $matches[1];
        } else {
            $numericPaketId = (int) $paketId;
        }
        
        $pelanggan = Pelanggan::all()->filter(function ($pelanggan) use ($numericPaketId, $paketId) {
            // Jika format baru (PLG001-USR1-PKT1)
            if (preg_match('/PLG\d+-USR\d+-(PKT\d+)/', $pelanggan->pelanggan_id, $matches)) {
                return $matches[1] === $paketId;
            }
            // Format lama (122) - digit ketiga = paket_id
            elseif (preg_match('/^\d{3}$/', $pelanggan->pelanggan_id)) {
                $currentPaketId = (int) substr($pelanggan->pelanggan_id, 2, 1);
                return $currentPaketId === $numericPaketId;
            }
            return false;
        });
        
        // Return sebagai collection
        return collect($pelanggan);
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'paket_id', 'paket_id');
    }
}
