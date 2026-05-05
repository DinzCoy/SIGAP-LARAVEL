<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Insert Role "Ketua Tim" (ID 7) if not exists
        DB::table('roles')->insertOrIgnore([
            'id'   => 7,
            'name' => 'Ketua Tim',
        ]);

        // 2. Add team_leader_id column to tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('team_leader_id')
                  ->nullable()
                  ->after('technician_id')
                  ->constrained('users')
                  ->nullOnDelete();
        });

        // 3. Update status ENUM to include new workflow statuses
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM(
                'Menunggu Pengecekan Pengelola',
                'Diteruskan ke Ketua Tim',
                'Ditugaskan ke Teknisi',
                'Open',
                'In Progress',
                'Menunggu Persetujuan Biaya',
                'Approved',
                'Selesai',
                'Dibatalkan'
            ) DEFAULT 'Menunggu Pengecekan Pengelola'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status enum
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM(
                'Menunggu Pengecekan Pengelola',
                'Diteruskan ke Teknisi',
                'Open',
                'In Progress',
                'Menunggu Persetujuan Biaya',
                'Approved',
                'Selesai',
                'Dibatalkan'
            ) DEFAULT 'Open'");
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['team_leader_id']);
            $table->dropColumn('team_leader_id');
        });

        DB::table('roles')->where('id', 7)->delete();
    }
};
