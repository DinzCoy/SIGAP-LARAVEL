<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_loans', function (Blueprint $table) {
            // loaned_at hanya diisi saat admin menyetujui,
            // bukan saat user mengajukan (status=pending).
            $table->timestamp('loaned_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('asset_loans', function (Blueprint $table) {
            $table->timestamp('loaned_at')->nullable(false)->change();
        });
    }
};
