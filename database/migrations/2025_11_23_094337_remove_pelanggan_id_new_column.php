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
        // Cek apakah kolom pelanggan_id_new ada
        $columns = DB::select('SHOW COLUMNS FROM pelanggan');
        $hasColumn = collect($columns)->pluck('Field')->contains('pelanggan_id_new');
        
        if ($hasColumn) {
            Schema::table('pelanggan', function (Blueprint $table) {
                $table->dropColumn('pelanggan_id_new');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: tambahkan kembali kolom jika diperlukan (tapi seharusnya tidak perlu)
        // Schema::table('pelanggan', function (Blueprint $table) {
        //     $table->string('pelanggan_id_new', 50)->nullable()->after('pelanggan_id');
        // });
    }
};
