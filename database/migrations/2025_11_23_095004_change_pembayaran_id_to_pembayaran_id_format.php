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
        // Cek apakah kolom pembayaran_id sudah ada
        $columns = DB::select('SHOW COLUMNS FROM pembayaran');
        $hasPembayaranId = collect($columns)->pluck('Field')->contains('pembayaran_id');
        
        // 1. Tambahkan kolom pembayaran_id jika belum ada (nullable sementara untuk migrasi data)
        if (!$hasPembayaranId) {
            Schema::table('pembayaran', function (Blueprint $table) {
                $table->string('pembayaran_id', 10)->nullable()->unique()->after('id');
            });
        }

        // 2. Generate pembayaran_id untuk data existing yang belum punya pembayaran_id: BYR1, BYR2, BYR3, ...
        $pembayarans = DB::table('pembayaran')->whereNull('pembayaran_id')->orWhere('pembayaran_id', '')->orderBy('id')->get();
        if ($pembayarans->count() > 0) {
            // Cari pembayaran_id terakhir yang sudah ada
            $lastPembayaran = DB::table('pembayaran')->whereNotNull('pembayaran_id')->where('pembayaran_id', '!=', '')->orderBy('pembayaran_id', 'desc')->first();
            $counter = 1;
            
            if ($lastPembayaran && preg_match('/BYR(\d+)/', $lastPembayaran->pembayaran_id, $matches)) {
                $counter = (int) $matches[1] + 1;
            }
            
            foreach ($pembayarans as $pembayaran) {
                $newPembayaranId = 'BYR' . $counter;
                DB::table('pembayaran')
                    ->where('id', $pembayaran->id)
                    ->update(['pembayaran_id' => $newPembayaranId]);
                $counter++;
            }
        }

        // 3. Set pembayaran_id sebagai NOT NULL setelah data diisi
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('pembayaran_id', 10)->nullable(false)->change();
        });

        // 4. Set pembayaran_id sebagai primary key di tabel pembayaran
        // Hapus auto increment dari kolom id terlebih dahulu
        DB::statement("ALTER TABLE pembayaran MODIFY id BIGINT UNSIGNED NOT NULL");
        
        // Drop primary key lama menggunakan DB statement
        DB::statement("ALTER TABLE pembayaran DROP PRIMARY KEY");
        
        // Set pembayaran_id sebagai primary key
        DB::statement("ALTER TABLE pembayaran ADD PRIMARY KEY (pembayaran_id)");

        // 5. Hapus kolom id lama dari tabel pembayaran
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: tambahkan kembali kolom id
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->id()->first();
        });

        // Extract numeric ID dari pembayaran_id (BYR1 -> 1, BYR2 -> 2, dst)
        DB::statement("
            UPDATE pembayaran 
            SET id = CAST(SUBSTRING(pembayaran_id, 4) AS UNSIGNED)
        ");

        // Set id sebagai primary key kembali
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropPrimary(['pembayaran_id']);
            $table->primary('id');
        });

        // Hapus kolom pembayaran_id
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn('pembayaran_id');
        });
    }
};
