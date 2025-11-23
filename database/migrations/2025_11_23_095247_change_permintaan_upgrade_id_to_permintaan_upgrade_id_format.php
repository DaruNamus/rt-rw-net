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
        // Cek apakah kolom permintaan_upgrade_id sudah ada
        $columns = DB::select('SHOW COLUMNS FROM permintaan_upgrade');
        $hasPermintaanUpgradeId = collect($columns)->pluck('Field')->contains('permintaan_upgrade_id');
        
        // 1. Tambahkan kolom permintaan_upgrade_id jika belum ada (nullable sementara untuk migrasi data)
        if (!$hasPermintaanUpgradeId) {
            Schema::table('permintaan_upgrade', function (Blueprint $table) {
                $table->string('permintaan_upgrade_id', 10)->nullable()->unique()->after('id');
            });
        }

        // 2. Generate permintaan_upgrade_id untuk data existing yang belum punya permintaan_upgrade_id: UPG1, UPG2, UPG3, ...
        $permintaanUpgrades = DB::table('permintaan_upgrade')->whereNull('permintaan_upgrade_id')->orWhere('permintaan_upgrade_id', '')->orderBy('id')->get();
        if ($permintaanUpgrades->count() > 0) {
            // Cari permintaan_upgrade_id terakhir yang sudah ada
            $lastPermintaanUpgrade = DB::table('permintaan_upgrade')->whereNotNull('permintaan_upgrade_id')->where('permintaan_upgrade_id', '!=', '')->orderBy('permintaan_upgrade_id', 'desc')->first();
            $counter = 1;
            
            if ($lastPermintaanUpgrade && preg_match('/UPG(\d+)/', $lastPermintaanUpgrade->permintaan_upgrade_id, $matches)) {
                $counter = (int) $matches[1] + 1;
            }
            
            foreach ($permintaanUpgrades as $permintaanUpgrade) {
                $newPermintaanUpgradeId = 'UPG' . $counter;
                DB::table('permintaan_upgrade')
                    ->where('id', $permintaanUpgrade->id)
                    ->update(['permintaan_upgrade_id' => $newPermintaanUpgradeId]);
                $counter++;
            }
        }

        // 3. Set permintaan_upgrade_id sebagai NOT NULL setelah data diisi
        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->string('permintaan_upgrade_id', 10)->nullable(false)->change();
        });

        // 4. Set permintaan_upgrade_id sebagai primary key di tabel permintaan_upgrade
        // Hapus auto increment dari kolom id terlebih dahulu
        DB::statement("ALTER TABLE permintaan_upgrade MODIFY id BIGINT UNSIGNED NOT NULL");
        
        // Drop primary key lama menggunakan DB statement
        DB::statement("ALTER TABLE permintaan_upgrade DROP PRIMARY KEY");
        
        // Set permintaan_upgrade_id sebagai primary key
        DB::statement("ALTER TABLE permintaan_upgrade ADD PRIMARY KEY (permintaan_upgrade_id)");

        // 5. Hapus kolom id lama dari tabel permintaan_upgrade
        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: tambahkan kembali kolom id
        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->id()->first();
        });

        // Extract numeric ID dari permintaan_upgrade_id (UPG1 -> 1, UPG2 -> 2, dst)
        DB::statement("
            UPDATE permintaan_upgrade 
            SET id = CAST(SUBSTRING(permintaan_upgrade_id, 4) AS UNSIGNED)
        ");

        // Set id sebagai primary key kembali
        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->dropPrimary(['permintaan_upgrade_id']);
            $table->primary('id');
        });

        // Hapus kolom permintaan_upgrade_id
        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->dropColumn('permintaan_upgrade_id');
        });
    }
};
