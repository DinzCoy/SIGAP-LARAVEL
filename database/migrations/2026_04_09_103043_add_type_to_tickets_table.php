<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('type', ['Asset', 'General'])->default('Asset')->after('id');

            $table->unsignedBigInteger('asset_id')->nullable()->change();
        });

        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('Menunggu Pengecekan Pengelola', 'Diteruskan ke Teknisi', 'Open', 'In Progress', 'Menunggu Persetujuan Biaya', 'Approved', 'Selesai', 'Dibatalkan') DEFAULT 'Open'");
        }
    }

    public function down(): void
    {

        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('Open', 'In Progress', 'Menunggu Persetujuan Biaya', 'Approved', 'Selesai', 'Dibatalkan') DEFAULT 'Open'");
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('asset_id')->nullable(false)->change();
            $table->dropColumn('type');
        });
    }
};
