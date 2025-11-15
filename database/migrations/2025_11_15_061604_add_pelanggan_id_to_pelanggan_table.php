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
        // Tambahkan kolom pelanggan_id (nullable sementara untuk migrasi data)
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->string('pelanggan_id', 10)->nullable()->unique()->after('id');
        });

        // Generate pelanggan_id untuk data existing
        // Format: id + user_id + paket_id (contoh: 1+2+2 = "122")
        DB::statement("
            UPDATE pelanggan 
            SET pelanggan_id = CONCAT(id, user_id, paket_id)
        ");

        // Set pelanggan_id sebagai NOT NULL setelah data diisi
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->string('pelanggan_id', 10)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropColumn('pelanggan_id');
        });
    }
};
