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
        // 0. Update panjang kolom pelanggan_id dulu (dari VARCHAR(10) ke VARCHAR(50))
        DB::statement("ALTER TABLE pelanggan MODIFY pelanggan_id VARCHAR(50) NOT NULL");
        
        // 1. Drop semua foreign key constraints dulu
        // Tagihan
        $foreignKeysTagihan = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'tagihan' 
            AND COLUMN_NAME = 'pelanggan_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (count($foreignKeysTagihan) > 0) {
            Schema::table('tagihan', function (Blueprint $table) {
                $table->dropForeign(['pelanggan_id']);
            });
        }
        
        // Pembayaran
        $foreignKeysPembayaran = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'pembayaran' 
            AND COLUMN_NAME = 'pelanggan_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (count($foreignKeysPembayaran) > 0) {
            Schema::table('pembayaran', function (Blueprint $table) {
                $table->dropForeign(['pelanggan_id']);
            });
        }
        
        // Permintaan Upgrade
        $foreignKeysUpgrade = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'permintaan_upgrade' 
            AND COLUMN_NAME = 'pelanggan_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (count($foreignKeysUpgrade) > 0) {
            Schema::table('permintaan_upgrade', function (Blueprint $table) {
                $table->dropForeign(['pelanggan_id']);
            });
        }
        
        // 2. Update panjang kolom di tabel terkait (dari VARCHAR(10) ke VARCHAR(50))
        DB::statement("ALTER TABLE tagihan MODIFY pelanggan_id VARCHAR(50) NOT NULL");
        DB::statement("ALTER TABLE pembayaran MODIFY pelanggan_id VARCHAR(50) NOT NULL");
        DB::statement("ALTER TABLE permintaan_upgrade MODIFY pelanggan_id VARCHAR(50) NOT NULL");
        
        // 3. Ambil semua pelanggan dan mapping data
        $pelanggans = DB::table('pelanggan')->get();
        $pelangganMappings = [];
        
        foreach ($pelanggans as $pelanggan) {
            $oldPelangganId = $pelanggan->pelanggan_id;
            
            // Extract id, user_id, paket_id dari format lama (contoh: "122")
            if (preg_match('/^(\d)(\d)(\d)$/', $oldPelangganId, $matches)) {
                $id = (int) $matches[1];
                $numericUserId = (int) $matches[2];
                $numericPaketId = (int) $matches[3];
                
                // Convert ke format baru
                $userId = 'USR' . $numericUserId;
                $paketId = 'PKT' . $numericPaketId;
                
                // Generate pelanggan_id baru: PLG001-USR2-PKT2
                $newPelangganId = sprintf('PLG%03d-%s-%s', $id, $userId, $paketId);
                
                $pelangganMappings[$oldPelangganId] = $newPelangganId;
            }
        }
        
        // 4. Update data di tabel terkait dulu (tagihan, pembayaran, permintaan_upgrade)
        // Update data tagihan - gunakan raw SQL dengan escape string
        foreach ($pelangganMappings as $oldId => $newId) {
            $escapedNewId = DB::getPdo()->quote($newId);
            $escapedOldId = DB::getPdo()->quote($oldId);
            $affected = DB::unprepared("UPDATE tagihan SET pelanggan_id = {$escapedNewId} WHERE pelanggan_id = {$escapedOldId}");
        }
        
        // Pastikan tidak ada data tagihan yang masih menggunakan format lama
        $remainingOldIds = DB::table('tagihan')
            ->whereIn('pelanggan_id', array_keys($pelangganMappings))
            ->pluck('pelanggan_id')
            ->unique()
            ->toArray();
            
        if (count($remainingOldIds) > 0) {
            foreach ($remainingOldIds as $oldId) {
                if (isset($pelangganMappings[$oldId])) {
                    $newId = $pelangganMappings[$oldId];
                    $escapedNewId = DB::getPdo()->quote($newId);
                    $escapedOldId = DB::getPdo()->quote($oldId);
                    DB::unprepared("UPDATE tagihan SET pelanggan_id = {$escapedNewId} WHERE pelanggan_id = {$escapedOldId}");
                }
            }
        }
        
        // Update data pembayaran
        foreach ($pelangganMappings as $oldId => $newId) {
            $escapedNewId = DB::getPdo()->quote($newId);
            $escapedOldId = DB::getPdo()->quote($oldId);
            DB::unprepared("UPDATE pembayaran SET pelanggan_id = {$escapedNewId} WHERE pelanggan_id = {$escapedOldId}");
        }
        
        // Update data permintaan_upgrade
        foreach ($pelangganMappings as $oldId => $newId) {
            $escapedNewId = DB::getPdo()->quote($newId);
            $escapedOldId = DB::getPdo()->quote($oldId);
            DB::unprepared("UPDATE permintaan_upgrade SET pelanggan_id = {$escapedNewId} WHERE pelanggan_id = {$escapedOldId}");
        }
        
        // 5. Update pelanggan_id di tabel pelanggan (setelah semua tabel terkait sudah diupdate)
        foreach ($pelangganMappings as $oldId => $newId) {
            $escapedNewId = DB::getPdo()->quote($newId);
            $escapedOldId = DB::getPdo()->quote($oldId);
            DB::unprepared("UPDATE pelanggan SET pelanggan_id = {$escapedNewId} WHERE pelanggan_id = {$escapedOldId}");
        }
        
        // 6. Recreate foreign key constraints
        Schema::table('tagihan', function (Blueprint $table) {
            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggan')->onDelete('cascade');
        });
        
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggan')->onDelete('cascade');
        });
        
        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: convert kembali ke format lama
        $pelanggans = DB::table('pelanggan')->get();
        
        foreach ($pelanggans as $pelanggan) {
            // Extract dari format baru: PLG001-USR2-PKT2 -> 122
            if (preg_match('/PLG(\d+)-USR(\d+)-PKT(\d+)/', $pelanggan->pelanggan_id, $matches)) {
                $id = (int) $matches[1];
                $userId = (int) $matches[2];
                $paketId = (int) $matches[3];
                
                $oldId = (string) $id . $userId . $paketId;
                
                DB::table('pelanggan')
                    ->where('pelanggan_id', $pelanggan->pelanggan_id)
                    ->update(['pelanggan_id' => $oldId]);
            }
        }
        
        // Rollback foreign keys
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
        });
        DB::statement("ALTER TABLE tagihan MODIFY pelanggan_id VARCHAR(10) NOT NULL");
        Schema::table('tagihan', function (Blueprint $table) {
            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggan')->onDelete('cascade');
        });
        
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
        });
        DB::statement("ALTER TABLE pembayaran MODIFY pelanggan_id VARCHAR(10) NOT NULL");
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggan')->onDelete('cascade');
        });
        
        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
        });
        DB::statement("ALTER TABLE permintaan_upgrade MODIFY pelanggan_id VARCHAR(10) NOT NULL");
        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggan')->onDelete('cascade');
        });
        
        DB::statement("ALTER TABLE pelanggan MODIFY pelanggan_id VARCHAR(10) NOT NULL");
    }
};
