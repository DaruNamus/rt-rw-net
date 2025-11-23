<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanUpgrade extends Model
{
    protected $table = 'permintaan_upgrade';

    protected $primaryKey = 'permintaan_upgrade_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'permintaan_upgrade_id',
        'pelanggan_id',
        'paket_lama_id',
        'paket_baru_id',
        'status',
        'alasan',
        'catatan_admin',
        'diproses_oleh',
        'diproses_pada',
    ];

    /**
     * Generate permintaan_upgrade_id baru
     * Format: UPG1, UPG2, UPG3, ...
     */
    public static function generatePermintaanUpgradeId(): string
    {
        // Ambil permintaan_upgrade_id terakhir
        $lastPermintaanUpgrade = static::orderBy('permintaan_upgrade_id', 'desc')->first();
        
        if ($lastPermintaanUpgrade && preg_match('/UPG(\d+)/', $lastPermintaanUpgrade->permintaan_upgrade_id, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'UPG' . $nextNumber;
    }

    protected function casts(): array
    {
        return [
            'diproses_pada' => 'datetime',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'pelanggan_id');
    }

    public function paketLama(): BelongsTo
    {
        return $this->belongsTo(Paket::class, 'paket_lama_id', 'paket_id');
    }

    public function paketBaru(): BelongsTo
    {
        return $this->belongsTo(Paket::class, 'paket_baru_id', 'paket_id');
    }

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh', 'user_id');
    }
}
