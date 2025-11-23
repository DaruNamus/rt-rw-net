<?php

/**
 * Script untuk mengembalikan ID migration di database ke format standar Laravel (integer).
 * 
 * Gunakan script ini setelah presentasi untuk mengembalikan ke format standar.
 * 
 * Cara menggunakan:
 * php scripts/revert_migrations_to_standard.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Script Revert Migration ID ke Format Standar ===\n\n";

try {
    DB::beginTransaction();
    
    // Ambil semua migration yang sudah ada, urutkan berdasarkan batch dan migration name
    $migrations = DB::table('migrations')->orderBy('batch')->orderBy('migration')->get();
    
    if ($migrations->isEmpty()) {
        echo "❌ ERROR: Tidak ada migration yang ditemukan.\n";
        exit(1);
    }
    
    // 1. Hapus primary key constraint (jika ada)
    try {
        DB::statement("ALTER TABLE migrations DROP PRIMARY KEY");
    } catch (\Exception $e) {
        // Primary key mungkin sudah tidak ada, tidak masalah
    }
    
    // 2. Update nilai id kembali ke integer menggunakan migration name sebagai identifier
    // Gunakan temporary id dulu untuk menghindari duplikasi
    echo "Mengubah ID kembali ke format integer...\n";
    
    // Step 1: Update semua ke temporary id (TEMP1, TEMP2, dst) menggunakan migration name
    $counter = 1;
    foreach ($migrations as $migration) {
        $tempId = 'TEMP' . $counter;
        DB::statement("UPDATE migrations SET id = ? WHERE migration = ?", [$tempId, $migration->migration]);
        $counter++;
    }
    
    // Step 2: Update dari temporary id ke integer (1, 2, 3, ...) menggunakan migration name
    $counter = 1;
    $updated = 0;
    foreach ($migrations as $migration) {
        $newId = $counter;
        DB::statement("UPDATE migrations SET id = ? WHERE migration = ?", [$newId, $migration->migration]);
        echo "✓ Reverted ID: → {$newId} ({$migration->migration})\n";
        $counter++;
        $updated++;
    }
    
    // 3. Ubah tipe kolom id kembali ke int unsigned (tanpa AUTO_INCREMENT dulu)
    echo "\nMengubah tipe kolom id kembali ke int unsigned...\n";
    DB::statement("ALTER TABLE migrations MODIFY id INT UNSIGNED NOT NULL");
    
    // 4. Set kembali sebagai primary key
    try {
        DB::statement("ALTER TABLE migrations ADD PRIMARY KEY (id)");
    } catch (\Exception $e) {
        // Primary key mungkin sudah ada
        if (strpos($e->getMessage(), 'Multiple primary key') === false && 
            strpos($e->getMessage(), 'Duplicate key name') === false) {
            throw $e;
        }
    }
    
    // 5. Tambahkan AUTO_INCREMENT setelah primary key sudah ada
    DB::statement("ALTER TABLE migrations MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT");
    
    // 6. Reset auto-increment ke nilai yang benar
    $maxId = DB::table('migrations')->max('id');
    if ($maxId) {
        DB::statement("ALTER TABLE migrations AUTO_INCREMENT = " . ($maxId + 1));
        echo "✓ AUTO_INCREMENT direset ke " . ($maxId + 1) . "\n";
    }
    
    // Hapus file mapping jika ada
    $mappingFile = __DIR__ . '/migration_id_mapping.json';
    if (file_exists($mappingFile)) {
        unlink($mappingFile);
        echo "✓ File mapping dihapus\n";
    }
    
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
    echo "Total migration ID yang direvert: {$updated}\n";
    echo "\nMigration ID sudah dikembalikan ke format standar Laravel (integer).\n";
    echo "Sekarang Anda bisa menggunakan 'php artisan migrate' dan 'php artisan migrate:rollback' dengan normal.\n";
    
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


