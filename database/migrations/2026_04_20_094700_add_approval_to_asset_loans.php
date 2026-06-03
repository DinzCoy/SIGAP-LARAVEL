<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE asset_loans MODIFY COLUMN status ENUM('pending','active','returned','rejected') DEFAULT 'pending'");
        }

        Schema::table('asset_loans', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('loaned_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('asset_loans', function (Blueprint $table) {
            $table->dropColumn(['approved_at', 'rejected_at']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE asset_loans MODIFY COLUMN status ENUM('active','returned') DEFAULT 'active'");
        }
    }
};
