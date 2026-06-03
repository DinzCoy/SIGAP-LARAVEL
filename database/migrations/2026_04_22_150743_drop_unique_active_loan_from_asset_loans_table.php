<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('asset_loans', function (Blueprint $table) {

            $table->index('asset_id', 'asset_loans_asset_id_index');
            $table->dropUnique('unique_active_loan');
        });
    }

    public function down(): void
    {
        Schema::table('asset_loans', function (Blueprint $table) {
            $table->unique(['asset_id', 'status'], 'unique_active_loan');
            $table->dropIndex('asset_loans_asset_id_index');
        });
    }
};
