<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    protected $table = 'tagihan';

    protected $fillable = [
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
        return $this->belongsTo(Paket::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }
}
