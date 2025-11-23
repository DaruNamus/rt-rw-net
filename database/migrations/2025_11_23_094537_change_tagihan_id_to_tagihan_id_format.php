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
        // Cek apakah kolom tagihan_id sudah ada
        $columns = DB::select('SHOW COLUMNS FROM tagihan');
        $hasTagihanId = collect($columns)->pluck('Field')->contains('tagihan_id');
        
        // 1. Tambahkan kolom tagihan_id jika belum ada (nullable sementara untuk migrasi data)
        if (!$hasTagihanId) {
            Schema::table('tagihan', function (Blueprint $table) {
                $table->string('tagihan_id', 10)->nullable()->unique()->after('id');
            });
        }

        // 2. Generate tagihan_id untuk data existing yang belum punya tagihan_id: TGH1, TGH2, TGH3, ...
        $tagihans = DB::table('tagihan')->whereNull('tagihan_id')->orWhere('tagihan_id', '')->orderBy('id')->get();
        if ($tagihans->count() > 0) {
            // Cari tagihan_id terakhir yang sudah ada
            $lastTagihan = DB::table('tagihan')->whereNotNull('tagihan_id')->where('tagihan_id', '!=', '')->orderBy('tagihan_id', 'desc')->first();
            $counter = 1;
            
            if ($lastTagihan && preg_match('/TGH(\d+)/', $lastTagihan->tagihan_id, $matches)) {
                $counter = (int) $matches[1] + 1;
            }
            
            foreach ($tagihans as $tagihan) {
                $newTagihanId = 'TGH' . $counter;
                DB::table('tagihan')
                    ->where('id', $tagihan->id)
                    ->update(['tagihan_id' => $newTagihanId]);
                $counter++;
            }
        }

        // 3. Set tagihan_id sebagai NOT NULL setelah data diisi
        Schema::table('tagihan', function (Blueprint $table) {
            $table->string('tagihan_id', 10)->nullable(false)->change();
        });

        // Simpan mapping id -> tagihan_id dulu sebelum ubah tipe kolom
        $tagihanMappings = DB::table('tagihan')->pluck('tagihan_id', 'id')->toArray();

        // 4. Update foreign key di tabel pembayaran (tagihan_id)
        // Cek apakah foreign key ada sebelum drop
        $foreignKeysPembayaran = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'pembayaran' 
            AND COLUMN_NAME = 'tagihan_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (count($foreignKeysPembayaran) > 0) {
            Schema::table('pembayaran', function (Blueprint $table) {
                // Drop foreign key constraint dulu
                $table->dropForeign(['tagihan_id']);
            });
        }

        // Ubah tipe kolom dulu (dari integer ke varchar)
        DB::statement("ALTER TABLE pembayaran MODIFY tagihan_id VARCHAR(10) NOT NULL");

        // Migrasi data: convert integer tagihan_id ke format string baru
        foreach ($tagihanMappings as $oldId => $newTagihanId) {
            $escapedNewId = DB::getPdo()->quote($newTagihanId);
            $escapedOldId = DB::getPdo()->quote($oldId);
            DB::unprepared("UPDATE pembayaran SET tagihan_id = {$escapedNewId} WHERE tagihan_id = {$escapedOldId}");
        }

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreign('tagihan_id')->references('tagihan_id')->on('tagihan')->onDelete('cascade');
        });

        // 5. Set tagihan_id sebagai primary key di tabel tagihan
        // Hapus auto increment dari kolom id terlebih dahulu
        DB::statement("ALTER TABLE tagihan MODIFY id BIGINT UNSIGNED NOT NULL");
        
        // Drop primary key lama menggunakan DB statement
        DB::statement("ALTER TABLE tagihan DROP PRIMARY KEY");
        
        // Set tagihan_id sebagai primary key
        DB::statement("ALTER TABLE tagihan ADD PRIMARY KEY (tagihan_id)");

        // 6. Hapus kolom id lama dari tabel tagihan
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: tambahkan kembali kolom id
        Schema::table('tagihan', function (Blueprint $table) {
            $table->id()->first();
        });

        // Extract numeric ID dari tagihan_id (TGH1 -> 1, TGH2 -> 2, dst)
        DB::statement("
            UPDATE tagihan 
            SET id = CAST(SUBSTRING(tagihan_id, 4) AS UNSIGNED)
        ");

        // Set id sebagai primary key kembali
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropPrimary(['tagihan_id']);
            $table->primary('id');
        });

        // Rollback foreign keys di tabel terkait
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['tagihan_id']);
        });

        // Convert kembali ke integer
        DB::statement("
            UPDATE pembayaran pb
            INNER JOIN tagihan t ON pb.tagihan_id = t.tagihan_id
            SET pb.tagihan_id = t.id
        ");

        DB::statement("ALTER TABLE pembayaran MODIFY tagihan_id BIGINT UNSIGNED NOT NULL");

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreign('tagihan_id')->references('id')->on('tagihan')->onDelete('cascade');
        });

        // Hapus kolom tagihan_id
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropColumn('tagihan_id');
        });
    }
};
