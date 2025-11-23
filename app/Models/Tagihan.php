<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    protected $table = 'tagihan';

    protected $primaryKey = 'tagihan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tagihan_id',
        'pelanggan_id',
        'paket_id',
        'bulan',
        'tahun',
        'jumlah_tagihan',
        'status',
        'tanggal_jatuh_tempo',
        'jenis_tagihan',
        'keterangan',
    ];

    /**
     * Generate tagihan_id baru
     * Format: TGH1, TGH2, TGH3, ...
     */
    public static function generateTagihanId(): string
    {
        // Ambil tagihan_id terakhir
        $lastTagihan = static::orderBy('tagihan_id', 'desc')->first();
        
        if ($lastTagihan && preg_match('/TGH(\d+)/', $lastTagihan->tagihan_id, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'TGH' . $nextNumber;
    }

    protected function casts(): array
    {
        return [
            'jumlah_tagihan' => 'decimal:2',
            'tanggal_jatuh_tempo' => 'date',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'pelanggan_id');
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(Paket::class, 'paket_id', 'paket_id');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_id', 'tagihan_id');
    }
}
