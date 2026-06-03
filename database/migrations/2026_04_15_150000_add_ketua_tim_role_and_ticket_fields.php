<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {

        DB::table('roles')->insertOrIgnore([
            'id'   => 7,
            'name' => 'Ketua Tim',
        ]);

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('team_leader_id')
                  ->nullable()
                  ->after('technician_id')
                  ->constrained('users')
                  ->nullOnDelete();
        });

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

    public function down(): void
    {

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
