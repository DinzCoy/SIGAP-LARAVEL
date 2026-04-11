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
        Schema::table('pc_reports', function (Blueprint $table) {
            $table->string('os_name')->nullable()->change();
            $table->integer('os_build')->nullable()->change();
            $table->bigInteger('ram_free_kb')->nullable()->change();
            $table->bigInteger('disk_free_b')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pc_reports', function (Blueprint $table) {
            $table->string('os_name')->nullable(false)->change();
            $table->integer('os_build')->nullable(false)->change();
            $table->bigInteger('ram_free_kb')->nullable(false)->change();
            $table->bigInteger('disk_free_b')->nullable(false)->change();
        });
    }
};
