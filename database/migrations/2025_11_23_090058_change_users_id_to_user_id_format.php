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
        // Cek apakah kolom user_id sudah ada
        $columns = DB::select('SHOW COLUMNS FROM users');
        $hasUserId = collect($columns)->pluck('Field')->contains('user_id');
        
        // 1. Tambahkan kolom user_id jika belum ada (nullable sementara untuk migrasi data)
        if (!$hasUserId) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('user_id', 10)->nullable()->unique()->after('id');
            });
        }

        // 2. Generate user_id untuk data existing yang belum punya user_id: USR1, USR2, USR3, ...
        $users = DB::table('users')->whereNull('user_id')->orWhere('user_id', '')->orderBy('id')->get();
        if ($users->count() > 0) {
            // Cari user_id terakhir yang sudah ada
            $lastUser = DB::table('users')->whereNotNull('user_id')->where('user_id', '!=', '')->orderBy('user_id', 'desc')->first();
            $counter = 1;
            
            if ($lastUser && preg_match('/USR(\d+)/', $lastUser->user_id, $matches)) {
                $counter = (int) $matches[1] + 1;
            }
            
            foreach ($users as $user) {
                $newUserId = 'USR' . $counter;
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['user_id' => $newUserId]);
                $counter++;
            }
        }

        // 3. Set user_id sebagai NOT NULL setelah data diisi
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_id', 10)->nullable(false)->change();
        });

        // 4. Update foreign key di tabel pembayaran (diverifikasi_oleh)
        // Cek apakah foreign key ada sebelum drop
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'pembayaran' 
            AND COLUMN_NAME = 'diverifikasi_oleh' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (count($foreignKeys) > 0) {
            Schema::table('pembayaran', function (Blueprint $table) {
                // Drop foreign key constraint dulu
                $table->dropForeign(['diverifikasi_oleh']);
            });
        }

        // Simpan mapping id -> user_id dulu sebelum ubah tipe kolom
        $userMappings = DB::table('users')->pluck('user_id', 'id')->toArray();

        // Ubah tipe kolom dulu (dari integer ke varchar) - MySQL akan convert otomatis
        DB::statement("ALTER TABLE pembayaran MODIFY diverifikasi_oleh VARCHAR(10) NULL");

        // Migrasi data: convert integer diverifikasi_oleh ke format string baru
        // Setelah tipe kolom sudah VARCHAR, nilai sudah jadi string, jadi kita perlu mapping manual
        foreach ($userMappings as $oldId => $newUserId) {
            DB::table('pembayaran')
                ->where('diverifikasi_oleh', (string) $oldId)
                ->update(['diverifikasi_oleh' => $newUserId]);
        }

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreign('diverifikasi_oleh')->references('user_id')->on('users')->onDelete('set null');
        });

        // 5. Update foreign key di tabel permintaan_upgrade (diproses_oleh)
        // Cek apakah foreign key ada sebelum drop
        $foreignKeysUpgrade = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'permintaan_upgrade' 
            AND COLUMN_NAME = 'diproses_oleh' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (count($foreignKeysUpgrade) > 0) {
            Schema::table('permintaan_upgrade', function (Blueprint $table) {
                // Drop foreign key constraint dulu
                $table->dropForeign(['diproses_oleh']);
            });
        }

        // Ubah tipe kolom dulu (dari integer ke varchar)
        DB::statement("ALTER TABLE permintaan_upgrade MODIFY diproses_oleh VARCHAR(10) NULL");

        // Migrasi data: convert integer diproses_oleh ke format string baru
        foreach ($userMappings as $oldId => $newUserId) {
            DB::table('permintaan_upgrade')
                ->where('diproses_oleh', (string) $oldId)
                ->update(['diproses_oleh' => $newUserId]);
        }

        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->foreign('diproses_oleh')->references('user_id')->on('users')->onDelete('set null');
        });

        // 6. Update foreign key di tabel sessions (user_id) - Laravel default
        // Cek apakah foreign key ada sebelum drop
        $foreignKeysSessions = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'sessions' 
            AND COLUMN_NAME = 'user_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (count($foreignKeysSessions) > 0) {
            Schema::table('sessions', function (Blueprint $table) {
                // Drop foreign key constraint jika ada
                $table->dropForeign(['user_id']);
            });
        }

        // Ubah tipe kolom dulu (dari integer ke varchar)
        DB::statement("ALTER TABLE sessions MODIFY user_id VARCHAR(10) NULL");

        // Migrasi data: convert integer user_id ke format string baru
        foreach ($userMappings as $oldId => $newUserId) {
            DB::table('sessions')
                ->where('user_id', (string) $oldId)
                ->update(['user_id' => $newUserId]);
        }

        // Cek apakah index sudah ada sebelum membuat
        $indexes = DB::select("
            SELECT INDEX_NAME 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'sessions' 
            AND INDEX_NAME = 'sessions_user_id_index'
        ");
        
        if (count($indexes) == 0) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->index('user_id');
            });
        }

        // 7. Set user_id sebagai primary key di tabel users
        // Hapus auto increment dari kolom id terlebih dahulu
        DB::statement("ALTER TABLE users MODIFY id BIGINT UNSIGNED NOT NULL");
        
        // Drop primary key lama menggunakan DB statement
        DB::statement("ALTER TABLE users DROP PRIMARY KEY");
        
        // Set user_id sebagai primary key
        DB::statement("ALTER TABLE users ADD PRIMARY KEY (user_id)");

        // 8. Hapus kolom id lama dari tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: tambahkan kembali kolom id
        Schema::table('users', function (Blueprint $table) {
            $table->id()->first();
        });

        // Extract numeric ID dari user_id (USR1 -> 1, USR2 -> 2, dst)
        DB::statement("
            UPDATE users 
            SET id = CAST(SUBSTRING(user_id, 4) AS UNSIGNED)
        ");

        // Set id sebagai primary key kembali
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary(['user_id']);
            $table->primary('id');
        });

        // Rollback foreign keys di tabel terkait
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['diverifikasi_oleh']);
        });

        // Convert kembali ke integer
        DB::statement("
            UPDATE pembayaran pb
            INNER JOIN users u ON pb.diverifikasi_oleh = u.user_id
            SET pb.diverifikasi_oleh = u.id
            WHERE pb.diverifikasi_oleh IS NOT NULL
        ");

        DB::statement("ALTER TABLE pembayaran MODIFY diverifikasi_oleh BIGINT UNSIGNED NULL");

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreign('diverifikasi_oleh')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->dropForeign(['diproses_oleh']);
        });

        DB::statement("
            UPDATE permintaan_upgrade pu
            INNER JOIN users u ON pu.diproses_oleh = u.user_id
            SET pu.diproses_oleh = u.id
            WHERE pu.diproses_oleh IS NOT NULL
        ");

        DB::statement("ALTER TABLE permintaan_upgrade MODIFY diproses_oleh BIGINT UNSIGNED NULL");

        Schema::table('permintaan_upgrade', function (Blueprint $table) {
            $table->foreign('diproses_oleh')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        DB::statement("
            UPDATE sessions s
            INNER JOIN users u ON s.user_id = u.user_id
            SET s.user_id = u.id
            WHERE s.user_id IS NOT NULL
        ");

        DB::statement("ALTER TABLE sessions MODIFY user_id BIGINT UNSIGNED NULL");

        Schema::table('sessions', function (Blueprint $table) {
            $table->index('user_id');
        });

        // Hapus kolom user_id
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
