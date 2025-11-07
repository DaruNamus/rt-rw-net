<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $fillable = [
        'user_id',
        'paket_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(Paket::class);
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function permintaanUpgrade(): HasMany
    {
        return $this->hasMany(PermintaanUpgrade::class);
    }
}
