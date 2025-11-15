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
        // 1. Update tabel tagihan: ubah pelanggan_id dari integer ke string
        Schema::table('tagihan', function (Blueprint $table) {
            // Drop foreign key constraint dulu
            $table->dropForeign(['pelanggan_id']);
        });

        // Migrasi data: convert integer pelanggan_id ke format string baru
        // Join berdasarkan id lama (yang masih ada di tabel pelanggan)
        DB::statement("
            UPDATE tagihan t
            INNER JOIN pelanggan p ON t.pelanggan_id = p.id
            SET t.pelanggan_id = p.pelanggan_id
        ");

        // Ubah tipe kolom menggunakan ALTER TABLE
        DB::statement("ALTER TABLE tagihan MODIFY pelanggan_id VARCHAR(10) NOT NULL");

        Schema::table('tagihan', function (Blueprint $table) {
            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggan')->onDelete('cascade');
        });

        // 2. Update tabel pembayaran: ubah pelanggan_id dari integer ke string
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
        });

        DB::statement("
            UPDATE pembayaran pb
            INNER JOIN pelanggan p ON pb.pelanggan_id = p.id
            SET pb.pelanggan_id = p.pelanggan_id
        ");

        DB::statement("ALTER TABLE pembayaran MODIFY pelanggan_id VARCHAR(10) NOT NULL");

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggan')->onDelete('cascade');
        });

        // 3. Update tabel permintaan_upgrade: ubah pelanggan_id dari integer ke string
        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
        });

        DB::statement("
            UPDATE permintaan_upgrade pu
            INNER JOIN pelanggan p ON pu.pelanggan_id = p.id
            SET pu.pelanggan_id = p.pelanggan_id
        ");

        DB::statement("ALTER TABLE permintaan_upgrade MODIFY pelanggan_id VARCHAR(10) NOT NULL");

        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggan')->onDelete('cascade');
        });

        // 4. Set pelanggan_id sebagai primary key di tabel pelanggan
        // Hapus auto increment dari kolom id terlebih dahulu
        DB::statement("ALTER TABLE pelanggan MODIFY id BIGINT UNSIGNED NOT NULL");
        
        // Drop primary key lama menggunakan DB statement
        DB::statement("ALTER TABLE pelanggan DROP PRIMARY KEY");
        
        // Set pelanggan_id sebagai primary key
        DB::statement("ALTER TABLE pelanggan ADD PRIMARY KEY (pelanggan_id)");

        // 5. Hapus kolom id, user_id, paket_id dari tabel pelanggan
        Schema::table('pelanggan', function (Blueprint $table) {
            // Drop foreign key constraints dulu
            $table->dropForeign(['user_id']);
            $table->dropForeign(['paket_id']);
            
            // Drop kolom
            $table->dropColumn(['id', 'user_id', 'paket_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: tambahkan kembali kolom id, user_id, paket_id
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->id()->first();
            $table->foreignId('user_id')->after('id')->constrained('users')->onDelete('cascade');
            $table->foreignId('paket_id')->after('user_id')->constrained('paket')->onDelete('restrict');
        });

        // Extract id, user_id, paket_id dari pelanggan_id
        DB::statement("
            UPDATE pelanggan 
            SET id = CAST(SUBSTRING(pelanggan_id, 1, 1) AS UNSIGNED),
                user_id = CAST(SUBSTRING(pelanggan_id, 2, 1) AS UNSIGNED),
                paket_id = CAST(SUBSTRING(pelanggan_id, 3, 1) AS UNSIGNED)
        ");

        // Set id sebagai primary key kembali
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropPrimary(['pelanggan_id']);
            $table->primary('id');
        });

        // Rollback foreign keys di tabel terkait
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
            $table->unsignedBigInteger('pelanggan_id')->change();
            $table->foreign('pelanggan_id')->references('id')->on('pelanggan')->onDelete('cascade');
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
            $table->unsignedBigInteger('pelanggan_id')->change();
            $table->foreign('pelanggan_id')->references('id')->on('pelanggan')->onDelete('cascade');
        });

        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->dropForeign(['pelanggan_id']);
            $table->unsignedBigInteger('pelanggan_id')->change();
            $table->foreign('pelanggan_id')->references('id')->on('pelanggan')->onDelete('cascade');
        });
    }
};
