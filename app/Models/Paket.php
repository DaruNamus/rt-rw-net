<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paket extends Model
{
    protected $table = 'paket';

    protected $fillable = [
        'nama_paket',
        'harga_bulanan',
        'harga_pemasangan',
        'kecepatan',
        'deskripsi',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'harga_bulanan' => 'decimal:2',
            'harga_pemasangan' => 'decimal:2',
        ];
    }

    public function pelanggan(): HasMany
    {
        return $this->hasMany(Pelanggan::class);
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }
}
