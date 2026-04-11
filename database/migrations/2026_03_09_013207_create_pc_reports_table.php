<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Table creation for 'pc_reports' to store PC monitoring data.
     */
    public function up(): void
    {
        Schema::create('pc_reports', function (Blueprint $table) {
            $table->id();
            $table->string('hostname');
            $table->string('ip_address');
            $table->string('mac_address')->unique();
            $table->string('os_name');
            $table->integer('os_build');
            $table->bigInteger('ram_free_kb');
            $table->bigInteger('disk_free_b');
            $table->timestamp('last_seen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pc_reports');
    }
};