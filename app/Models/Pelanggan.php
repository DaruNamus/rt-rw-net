<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $primaryKey = 'pelanggan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pelanggan_id',
        'alamat',
        'no_telepon',
        'tanggal_pemasangan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pemasangan' => 'date',
        ];
    }

    /**
     * Generate pelanggan_id baru
     * Format: id + user_id + paket_id (contoh: "122" = id=1, user_id=2, paket_id=2)
     */
    public static function generatePelangganId(int $userId, int $paketId): string
    {
        // Ambil ID terakhir dari pelanggan_id (digit pertama)
        $lastId = static::max('pelanggan_id');
        $nextId = $lastId ? (int) substr($lastId, 0, 1) + 1 : 1;
        
        // Pastikan tidak melebihi 9 (karena format 1 digit)
        if ($nextId > 9) {
            throw new \Exception('ID pelanggan sudah mencapai batas maksimal (9)');
        }
        
        // Pastikan user_id dan paket_id tidak melebihi 9
        if ($userId > 9 || $paketId > 9) {
            throw new \Exception('User ID atau Paket ID tidak boleh melebihi 9');
        }
        
        return (string) $nextId . $userId . $paketId;
    }

    /**
     * Accessor: Ambil user_id dari pelanggan_id
     * Format: digit kedua (contoh: "122" → user_id = 2)
     */
    public function getUserIdAttribute(): int
    {
        return (int) substr($this->pelanggan_id, 1, 1);
    }

    /**
     * Accessor: Ambil paket_id dari pelanggan_id
     * Format: digit ketiga (contoh: "122" → paket_id = 2)
     */
    public function getPaketIdAttribute(): int
    {
        return (int) substr($this->pelanggan_id, 2, 1);
    }

    /**
     * Helper method untuk mendapatkan User berdasarkan user_id dari pelanggan_id
     * Gunakan method ini untuk mendapatkan user (karena tidak ada foreign key)
     */
    public function getUser()
    {
        return User::find($this->user_id);
    }

    /**
     * Helper method untuk mendapatkan Paket berdasarkan paket_id dari pelanggan_id
     * Gunakan method ini untuk mendapatkan paket (karena tidak ada foreign key)
     */
    public function getPaket()
    {
        return Paket::find($this->paket_id);
    }

    /**
     * Accessor untuk relasi user (untuk kompatibilitas dengan kode yang sudah ada)
     * Menggunakan helper method getUser()
     */
    public function getUserAttribute()
    {
        return $this->getUser();
    }

    /**
     * Accessor untuk relasi paket (untuk kompatibilitas dengan kode yang sudah ada)
     * Menggunakan helper method getPaket()
     */
    public function getPaketAttribute()
    {
        return $this->getPaket();
    }

    /**
     * Helper method untuk mencari pelanggan berdasarkan user_id
     */
    public static function findByUserId(int $userId): ?self
    {
        return static::all()->first(function ($pelanggan) use ($userId) {
            return $pelanggan->user_id === $userId;
        });
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'pelanggan_id', 'pelanggan_id');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'pelanggan_id', 'pelanggan_id');
    }

    public function permintaanUpgrade(): HasMany
    {
        return $this->hasMany(PermintaanUpgrade::class, 'pelanggan_id', 'pelanggan_id');
    }
}
