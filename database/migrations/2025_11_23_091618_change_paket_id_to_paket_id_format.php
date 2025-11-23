<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek apakah kolom paket_id sudah ada
        $columns = DB::select('SHOW COLUMNS FROM paket');
        $hasPaketId = collect($columns)->pluck('Field')->contains('paket_id');
        
        // 1. Tambahkan kolom paket_id jika belum ada (nullable sementara untuk migrasi data)
        if (!$hasPaketId) {
            Schema::table('paket', function (Blueprint $table) {
                $table->string('paket_id', 10)->nullable()->unique()->after('id');
            });
        }

        // 2. Generate paket_id untuk data existing yang belum punya paket_id: PKT1, PKT2, PKT3, ...
        $pakets = DB::table('paket')->whereNull('paket_id')->orWhere('paket_id', '')->orderBy('id')->get();
        if ($pakets->count() > 0) {
            // Cari paket_id terakhir yang sudah ada
            $lastPaket = DB::table('paket')->whereNotNull('paket_id')->where('paket_id', '!=', '')->orderBy('paket_id', 'desc')->first();
            $counter = 1;
            
            if ($lastPaket && preg_match('/PKT(\d+)/', $lastPaket->paket_id, $matches)) {
                $counter = (int) $matches[1] + 1;
            }
            
            foreach ($pakets as $paket) {
                $newPaketId = 'PKT' . $counter;
                DB::table('paket')
                    ->where('id', $paket->id)
                    ->update(['paket_id' => $newPaketId]);
                $counter++;
            }
        }

        // 3. Set paket_id sebagai NOT NULL setelah data diisi
        Schema::table('paket', function (Blueprint $table) {
            $table->string('paket_id', 10)->nullable(false)->change();
        });

        // Simpan mapping id -> paket_id dulu sebelum ubah tipe kolom
        $paketMappings = DB::table('paket')->pluck('paket_id', 'id')->toArray();

        // 4. Update foreign key di tabel tagihan (paket_id)
        // Cek apakah foreign key ada sebelum drop
        $foreignKeysTagihan = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'tagihan' 
            AND COLUMN_NAME = 'paket_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (count($foreignKeysTagihan) > 0) {
            Schema::table('tagihan', function (Blueprint $table) {
                // Drop foreign key constraint dulu
                $table->dropForeign(['paket_id']);
            });
        }

        // Ubah tipe kolom dulu (dari integer ke varchar)
        DB::statement("ALTER TABLE tagihan MODIFY paket_id VARCHAR(10) NOT NULL");

        // Migrasi data: convert integer paket_id ke format string baru
        foreach ($paketMappings as $oldId => $newPaketId) {
            DB::table('tagihan')
                ->where('paket_id', (string) $oldId)
                ->update(['paket_id' => $newPaketId]);
        }

        Schema::table('tagihan', function (Blueprint $table) {
            $table->foreign('paket_id')->references('paket_id')->on('paket')->onDelete('restrict');
        });

        // 5. Update foreign key di tabel permintaan_upgrade (paket_lama_id dan paket_baru_id)
        // Cek apakah foreign key ada sebelum drop
        $foreignKeysUpgradeLama = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'permintaan_upgrade' 
            AND COLUMN_NAME = 'paket_lama_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        $foreignKeysUpgradeBaru = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'permintaan_upgrade' 
            AND COLUMN_NAME = 'paket_baru_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (count($foreignKeysUpgradeLama) > 0) {
            Schema::table('permintaan_upgrade', function (Blueprint $table) {
                $table->dropForeign(['paket_lama_id']);
            });
        }
        
        if (count($foreignKeysUpgradeBaru) > 0) {
            Schema::table('permintaan_upgrade', function (Blueprint $table) {
                $table->dropForeign(['paket_baru_id']);
            });
        }

        // Ubah tipe kolom dulu (dari integer ke varchar)
        DB::statement("ALTER TABLE permintaan_upgrade MODIFY paket_lama_id VARCHAR(10) NOT NULL");
        DB::statement("ALTER TABLE permintaan_upgrade MODIFY paket_baru_id VARCHAR(10) NOT NULL");

        // Migrasi data: convert integer paket_lama_id dan paket_baru_id ke format string baru
        foreach ($paketMappings as $oldId => $newPaketId) {
            DB::table('permintaan_upgrade')
                ->where('paket_lama_id', (string) $oldId)
                ->update(['paket_lama_id' => $newPaketId]);
                
            DB::table('permintaan_upgrade')
                ->where('paket_baru_id', (string) $oldId)
                ->update(['paket_baru_id' => $newPaketId]);
        }

        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->foreign('paket_lama_id')->references('paket_id')->on('paket')->onDelete('restrict');
            $table->foreign('paket_baru_id')->references('paket_id')->on('paket')->onDelete('restrict');
        });

        // 6. Set paket_id sebagai primary key di tabel paket
        // Hapus auto increment dari kolom id terlebih dahulu
        DB::statement("ALTER TABLE paket MODIFY id BIGINT UNSIGNED NOT NULL");
        
        // Drop primary key lama menggunakan DB statement
        DB::statement("ALTER TABLE paket DROP PRIMARY KEY");
        
        // Set paket_id sebagai primary key
        DB::statement("ALTER TABLE paket ADD PRIMARY KEY (paket_id)");

        // 7. Hapus kolom id lama dari tabel paket
        Schema::table('paket', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: tambahkan kembali kolom id
        Schema::table('paket', function (Blueprint $table) {
            $table->id()->first();
        });

        // Extract numeric ID dari paket_id (PKT1 -> 1, PKT2 -> 2, dst)
        DB::statement("
            UPDATE paket 
            SET id = CAST(SUBSTRING(paket_id, 4) AS UNSIGNED)
        ");

        // Set id sebagai primary key kembali
        Schema::table('paket', function (Blueprint $table) {
            $table->dropPrimary(['paket_id']);
            $table->primary('id');
        });

        // Rollback foreign keys di tabel terkait
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropForeign(['paket_id']);
        });

        // Convert kembali ke integer
        DB::statement("
            UPDATE tagihan t
            INNER JOIN paket p ON t.paket_id = p.paket_id
            SET t.paket_id = p.id
        ");

        DB::statement("ALTER TABLE tagihan MODIFY paket_id BIGINT UNSIGNED NOT NULL");

        Schema::table('tagihan', function (Blueprint $table) {
            $table->foreign('paket_id')->references('id')->on('paket')->onDelete('restrict');
        });

        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->dropForeign(['paket_lama_id']);
            $table->dropForeign(['paket_baru_id']);
        });

        DB::statement("
            UPDATE permintaan_upgrade pu
            INNER JOIN paket p ON pu.paket_lama_id = p.paket_id
            SET pu.paket_lama_id = p.id
        ");

        DB::statement("
            UPDATE permintaan_upgrade pu
            INNER JOIN paket p ON pu.paket_baru_id = p.paket_id
            SET pu.paket_baru_id = p.id
        ");

        DB::statement("ALTER TABLE permintaan_upgrade MODIFY paket_lama_id BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE permintaan_upgrade MODIFY paket_baru_id BIGINT UNSIGNED NOT NULL");

        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->foreign('paket_lama_id')->references('id')->on('paket')->onDelete('restrict');
            $table->foreign('paket_baru_id')->references('id')->on('paket')->onDelete('restrict');
        });

        // Hapus kolom paket_id
        Schema::table('paket', function (Blueprint $table) {
            $table->dropColumn('paket_id');
        });
    }
};
