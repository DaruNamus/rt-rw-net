<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanUpgrade extends Model
{
    protected $table = 'permintaan_upgrade';

    protected $fillable = [
        'pelanggan_id',
        'paket_lama_id',
        'paket_baru_id',
        'status',
        'alasan',
        'catatan_admin',
        'diproses_oleh',
        'diproses_pada',
    ];

    protected function casts(): array
    {
        return [
            'diproses_pada' => 'datetime',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function paketLama(): BelongsTo
    {
        return $this->belongsTo(Paket::class, 'paket_lama_id');
    }

    public function paketBaru(): BelongsTo
    {
        return $this->belongsTo(Paket::class, 'paket_baru_id');
    }

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
