<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $primaryKey = 'pembayaran_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pembayaran_id',
        'tagihan_id',
        'pelanggan_id',
        'jumlah_bayar',
        'tanggal_bayar',
        'bukti_pembayaran',
        'status',
        'catatan_admin',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ];

    /**
     * Generate pembayaran_id baru
     * Format: BYR1, BYR2, BYR3, ...
     */
    public static function generatePembayaranId(): string
    {
        // Ambil pembayaran_id terakhir berdasarkan nilai numeriknya
        $lastPembayaran = static::orderByRaw('CAST(SUBSTRING(pembayaran_id, 4) AS UNSIGNED) DESC')->first();
        
        if ($lastPembayaran && preg_match('/BYR(\d+)/', $lastPembayaran->pembayaran_id, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'BYR' . $nextNumber;
    }

    protected function casts(): array
    {
        return [
            'jumlah_bayar' => 'decimal:2',
            'tanggal_bayar' => 'date',
            'diverifikasi_pada' => 'datetime',
        ];
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id', 'tagihan_id');
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'pelanggan_id');
    }

    public function diverifikasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh', 'user_id');
    }
}
