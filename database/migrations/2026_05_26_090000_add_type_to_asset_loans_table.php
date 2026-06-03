<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_loans', function (Blueprint $table) {
            $table->string('type')->default('pinjam')->after('loan_reason');

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::table('asset_loans', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }
};
