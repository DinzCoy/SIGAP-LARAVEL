<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('type', ['Asset', 'General'])->default('Asset')->after('id');
            // Make foreign key nullable. First we might just alter the column itself.
            // Since it's already a foreign key, making it nullable usually just works via change() in newer Laravel.
            $table->unsignedBigInteger('asset_id')->nullable()->change();
        });

        // Use raw SQL to safely modify the ENUM for the status column
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('Menunggu Pengecekan Pengelola', 'Diteruskan ke Teknisi', 'Open', 'In Progress', 'Menunggu Persetujuan Biaya', 'Approved', 'Selesai', 'Dibatalkan') DEFAULT 'Open'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status enum to original
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('Open', 'In Progress', 'Menunggu Persetujuan Biaya', 'Approved', 'Selesai', 'Dibatalkan') DEFAULT 'Open'");
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('asset_id')->nullable(false)->change();
            $table->dropColumn('type');
        });
    }
};
