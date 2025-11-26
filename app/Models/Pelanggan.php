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
     * Format baru: PLG001-USR1-PKT1 (id pelanggan 1, User USR1, Paket PKT1)
     * Menerima user_id dan paket_id dalam format string (USR1, PKT1) atau integer
     */
    public static function generatePelangganId(string|int $userId, string|int $paketId): string
    {
        // Normalize user_id: extract dari USR1, USR2, dst atau gunakan langsung jika integer
        if (is_string($userId) && preg_match('/USR(\d+)/', $userId, $matches)) {
            $userIdFormatted = $userId; // USR1, USR2, dst
        } else {
            $numericUserId = (int) $userId;
            $userIdFormatted = 'USR' . $numericUserId;
        }
        
        // Normalize paket_id: extract dari PKT1, PKT2, dst atau gunakan langsung jika integer
        if (is_string($paketId) && preg_match('/PKT(\d+)/', $paketId, $matches)) {
            $paketIdFormatted = $paketId; // PKT1, PKT2, dst
        } else {
            $numericPaketId = (int) $paketId;
            $paketIdFormatted = 'PKT' . $numericPaketId;
        }
        
        // Ambil ID terakhir dari pelanggan_id berdasarkan nilai numeriknya
        $lastPelanggan = static::orderByRaw(
            "CAST(SUBSTRING_INDEX(SUBSTRING(pelanggan_id, 4), '-', 1) AS UNSIGNED) DESC"
        )->first();
        $nextId = 1;
        
        if ($lastPelanggan) {
            // Jika format baru (PLG001-USR1-PKT1)
            if (preg_match('/PLG(\d+)-/', $lastPelanggan->pelanggan_id, $matches)) {
                $nextId = (int) $matches[1] + 1;
            }
            // Jika format lama (122)
            elseif (preg_match('/^(\d)\d{2}$/', $lastPelanggan->pelanggan_id, $matches)) {
                $nextId = (int) $matches[1] + 1;
            }
        }
        
        // Format: PLG001-USR1-PKT1
        return sprintf('PLG%03d-%s-%s', $nextId, $userIdFormatted, $paketIdFormatted);
    }

    /**
     * Accessor: Ambil user_id dari pelanggan_id
     * Format lama: digit kedua (contoh: "122" → user_id = 2)
     * Format baru: extract dari PLG001-USR1-PKT1 → USR1
     */
    public function getUserIdAttribute(): string|int
    {
        // Jika format baru (PLG001-USR1-PKT1)
        if (preg_match('/PLG\d+-USR(\d+)-/', $this->pelanggan_id, $matches)) {
            return 'USR' . $matches[1];
        }
        // Format lama (122) - return integer untuk kompatibilitas
        return (int) substr($this->pelanggan_id, 1, 1);
    }

    /**
     * Accessor: Ambil paket_id dari pelanggan_id
     * Format lama: digit ketiga (contoh: "122" → paket_id = 2)
     * Format baru: extract dari PLG001-USR1-PKT1 → PKT1
     */
    public function getPaketIdAttribute(): string|int
    {
        // Jika format baru (PLG001-USR1-PKT1)
        if (preg_match('/PLG\d+-USR\d+-(PKT\d+)/', $this->pelanggan_id, $matches)) {
            return $matches[1]; // PKT1, PKT2, dst
        }
        // Format lama (122) - return integer untuk kompatibilitas
        return (int) substr($this->pelanggan_id, 2, 1);
    }

    /**
     * Helper method untuk mendapatkan User berdasarkan user_id dari pelanggan_id
     * Gunakan method ini untuk mendapatkan user (karena tidak ada foreign key)
     */
    public function getUser()
    {
        $userId = $this->user_id;
        // Jika format baru (USR1, USR2, dst), langsung find
        if (is_string($userId) && preg_match('/USR\d+/', $userId)) {
            return User::find($userId);
        }
        // Jika format lama (integer), convert ke format baru dulu
        // Tapi seharusnya tidak terjadi karena sudah diupdate
        return User::find('USR' . $userId);
    }

    /**
     * Helper method untuk mendapatkan Paket berdasarkan paket_id dari pelanggan_id
     * Gunakan method ini untuk mendapatkan paket (karena tidak ada foreign key)
     */
    public function getPaket()
    {
        $paketId = $this->paket_id;
        // Jika format baru (PKT1, PKT2, dst), langsung find
        if (is_string($paketId) && preg_match('/PKT\d+/', $paketId)) {
            return Paket::find($paketId);
        }
        // Jika format lama (integer), convert ke format baru dulu
        // Tapi seharusnya tidak terjadi karena sudah diupdate
        return Paket::find('PKT' . $paketId);
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
     * Menerima user_id dalam format string (USR1, USR2, dst) atau integer
     */
    public static function findByUserId(string|int $userId): ?self
    {
        // Normalize user_id: extract numeric ID jika format USR1, USR2, dst
        $numericUserId = null;
        $stringUserId = null;
        
        if (is_string($userId) && preg_match('/USR(\d+)/', $userId, $matches)) {
            $numericUserId = (int) $matches[1];
            $stringUserId = $userId; // USR1, USR2, dst
        } else {
            $numericUserId = (int) $userId;
            $stringUserId = 'USR' . $numericUserId;
        }
        
        // Cari pelanggan berdasarkan format lama atau baru
        return static::all()->first(function ($pelanggan) use ($numericUserId, $stringUserId) {
            // Jika format baru (PLG001-USR1-PKT1), extract dari bagian USR
            if (preg_match('/PLG\d+-USR(\d+)-/', $pelanggan->pelanggan_id, $matches)) {
                return (int) $matches[1] === $numericUserId;
            }
            // Jika format lama (numeric 3 digit seperti "122")
            elseif (preg_match('/^\d{3}$/', $pelanggan->pelanggan_id)) {
                $currentUserId = (int) substr($pelanggan->pelanggan_id, 1, 1);
                return $currentUserId === $numericUserId;
            }
            return false;
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
