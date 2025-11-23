<?php

/**
 * Script untuk mengubah ID migration di database menjadi format MGR1, MGR2, dst.
 * 
 * PERINGATAN:
 * - Script ini hanya untuk keperluan presentasi ke dosen
 * - JANGAN gunakan di production
 * - Pastikan sudah backup database sebelum menjalankan script ini
 * - Setelah presentasi, gunakan script revert untuk mengembalikan ke format standar
 * 
 * Cara menggunakan:
 * 1. Backup database terlebih dahulu
 * 2. Jalankan: php scripts/rename_migrations_to_mgr_format.php
 * 3. Setelah presentasi, jalankan: php scripts/revert_migrations_to_standard.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Script Rename Migration ID ke Format MGR ===\n\n";

try {
    DB::beginTransaction();
    
    // Cek tipe kolom id saat ini
    $columnInfo = DB::select("SHOW COLUMNS FROM migrations WHERE Field = 'id'");
    $currentType = $columnInfo[0]->Type ?? '';
    $isVarchar = strpos(strtolower($currentType), 'varchar') !== false;
    
    // Simpan mapping id lama ke id baru untuk revert
    $idMapping = [];
    
    // Ambil semua migration yang sudah ada, urutkan berdasarkan batch dan migration name
    // Urutan berdasarkan batch dulu, lalu migration name untuk konsistensi
    if ($isVarchar) {
        // Jika sudah varchar, ambil semua dan urutkan berdasarkan batch dan migration name
        $migrations = DB::select("SELECT * FROM migrations ORDER BY batch, migration");
        $migrations = collect($migrations);
    } else {
        // Jika masih int, ambil dan urutkan berdasarkan batch dan migration name
        $migrations = DB::table('migrations')->orderBy('batch')->orderBy('migration')->get();
    }
    
    if (!$isVarchar) {
        echo "Mengubah tipe kolom id dari int ke varchar...\n";
        
        // 1. Hapus auto-increment terlebih dahulu (harus dilakukan sebelum drop primary key)
        DB::statement("ALTER TABLE migrations MODIFY id INT UNSIGNED NOT NULL");
        
        // 2. Hapus primary key constraint (jika ada)
        try {
            DB::statement("ALTER TABLE migrations DROP PRIMARY KEY");
        } catch (\Exception $e) {
            // Primary key mungkin sudah tidak ada
        }
        
        // 3. Ubah tipe kolom id dari int ke varchar
        DB::statement("ALTER TABLE migrations MODIFY id VARCHAR(10) NOT NULL");
    } else {
        echo "Kolom id sudah bertipe varchar, melanjutkan update...\n";
        
        // Pastikan primary key sudah dihapus
        try {
            DB::statement("ALTER TABLE migrations DROP PRIMARY KEY");
        } catch (\Exception $e) {
            // Primary key mungkin sudah tidak ada
        }
    }
    
    // 4. Update nilai id menjadi MGR1, MGR2, dst.
    // Gunakan temporary id dulu untuk menghindari duplikasi
    // Step 1: Update semua ke temporary id (TEMP1, TEMP2, dst) menggunakan migration name sebagai identifier
    $counter = 1;
    $tempMappings = [];
    foreach ($migrations as $migration) {
        $oldId = is_object($migration) ? $migration->id : $migration['id'];
        $migrationName = is_object($migration) ? $migration->migration : $migration['migration'];
        $tempId = 'TEMP' . $counter;
        
        // Simpan mapping untuk revert (simpan ID integer asli jika ada)
        // Extract integer dari oldId jika mungkin (untuk referensi)
        $originalId = is_numeric($oldId) ? (int)$oldId : $counter;
        $idMapping[$migrationName] = $originalId; // Simpan berdasarkan migration name
        $tempMappings[$tempId] = $migrationName;
        
        // Update ke temporary id menggunakan migration name untuk memastikan tepat sasaran
        DB::statement("UPDATE migrations SET id = ? WHERE migration = ?", [$tempId, $migrationName]);
        $counter++;
    }
    
    // Step 2: Update dari temporary id ke MGR format menggunakan migration name
    $counter = 1;
    foreach ($tempMappings as $tempId => $migrationName) {
        $newId = 'MGR' . $counter;
        DB::statement("UPDATE migrations SET id = ? WHERE migration = ?", [$newId, $migrationName]);
        echo "✓ Updated ID: → {$newId} ({$migrationName})\n";
        $counter++;
    }
    
    // 5. Set kembali sebagai primary key (jika belum ada)
    try {
        DB::statement("ALTER TABLE migrations ADD PRIMARY KEY (id)");
    } catch (\Exception $e) {
        // Primary key mungkin sudah ada, tidak masalah
        if (strpos($e->getMessage(), 'Multiple primary key') === false && 
            strpos($e->getMessage(), 'Duplicate key name') === false) {
            throw $e;
        }
    }
    
    // Simpan mapping ke file untuk revert
    $mappingFile = __DIR__ . '/migration_id_mapping.json';
    file_put_contents($mappingFile, json_encode($idMapping, JSON_PRETTY_PRINT));
    echo "\n✓ Mapping disimpan ke: {$mappingFile}\n";
    
    // Commit transaction jika masih aktif
    try {
        if (DB::transactionLevel() > 0) {
            DB::commit();
        }
    } catch (\Exception $e) {
        // Transaction mungkin sudah di-commit atau tidak aktif, tidak masalah
        // Lanjutkan saja
    }
    
    echo "\n=== Selesai ===\n";
    echo "Total migration ID yang diupdate: " . count($migrations) . "\n";
    echo "\nPERINGATAN:\n";
    echo "- Jangan jalankan 'php artisan migrate' atau 'php artisan migrate:rollback' setelah ini\n";
    echo "- Gunakan script revert untuk mengembalikan ke format standar setelah presentasi\n";
    echo "- Script revert: php scripts/revert_migrations_to_standard.php\n";
    
} catch (\Exception $e) {
    // Rollback hanya jika transaction masih aktif
    try {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }
    } catch (\Exception $rollbackException) {
        // Ignore rollback error
    }
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

