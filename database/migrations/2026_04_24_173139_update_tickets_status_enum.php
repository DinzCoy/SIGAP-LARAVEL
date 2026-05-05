<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //Memperbaiki nilai ENUM kolom status agar konsisten dengan logika aplikasi.
    public function up(): void
    {
        // Perbaiki data lama yang pakai nama status tidak konsisten
        DB::statement("UPDATE tickets SET status = 'Diteruskan ke Teknisi' WHERE status = 'Ditugaskan ke Teknisi'");

        // Update ENUM dengan daftar status lengkap dan konsisten
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM(
                'Open',
                'Menunggu Pengecekan Pengelola',
                'Diteruskan ke Ketua Tim',
                'Diteruskan ke Teknisi',
                'In Progress',
                'Menunggu Persetujuan Biaya',
                'Approved',
                'Selesai',
                'Dibatalkan'
            ) DEFAULT 'Menunggu Pengecekan Pengelola'");
        }
    }

    //Rollback: kembalikan ke ENUM lama jika perlu.
    public function down(): void
    {
        DB::statement("UPDATE tickets SET status = 'Ditugaskan ke Teknisi' WHERE status = 'Diteruskan ke Teknisi'");

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
};
