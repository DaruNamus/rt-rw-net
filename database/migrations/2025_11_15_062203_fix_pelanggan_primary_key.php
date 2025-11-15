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
        // Cek apakah primary key sudah diubah
        $primaryKey = DB::select("SHOW KEYS FROM pelanggan WHERE Key_name = 'PRIMARY'");
        
        if (count($primaryKey) > 0 && $primaryKey[0]->Column_name === 'id') {
            // Primary key masih id, perlu diubah
            
            // 1. Hapus auto increment dari kolom id terlebih dahulu
            DB::statement("ALTER TABLE pelanggan MODIFY id BIGINT UNSIGNED NOT NULL");
            
            // 2. Drop primary key lama
            DB::statement("ALTER TABLE pelanggan DROP PRIMARY KEY");
            
            // 3. Set pelanggan_id sebagai primary key
            DB::statement("ALTER TABLE pelanggan ADD PRIMARY KEY (pelanggan_id)");
        }
        
        // Cek apakah kolom id, user_id, paket_id masih ada
        $columns = DB::select("SHOW COLUMNS FROM pelanggan");
        $columnNames = array_column($columns, 'Field');
        
        if (in_array('id', $columnNames) || in_array('user_id', $columnNames) || in_array('paket_id', $columnNames)) {
            // Kolom masih ada, perlu dihapus
            Schema::table('pelanggan', function (Blueprint $table) use ($columnNames) {
                // Drop foreign key constraints jika masih ada
                if (in_array('user_id', $columnNames)) {
                    try {
                        $table->dropForeign(['user_id']);
                    } catch (\Exception $e) {
                        // Foreign key mungkin sudah dihapus
                    }
                }
                
                if (in_array('paket_id', $columnNames)) {
                    try {
                        $table->dropForeign(['paket_id']);
                    } catch (\Exception $e) {
                        // Foreign key mungkin sudah dihapus
                    }
                }
                
                // Drop kolom yang masih ada
                $columnsToDrop = [];
                if (in_array('id', $columnNames)) {
                    $columnsToDrop[] = 'id';
                }
                if (in_array('user_id', $columnNames)) {
                    $columnsToDrop[] = 'user_id';
                }
                if (in_array('paket_id', $columnNames)) {
                    $columnsToDrop[] = 'paket_id';
                }
                
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback tidak diperlukan untuk migrasi perbaikan
    }
};
