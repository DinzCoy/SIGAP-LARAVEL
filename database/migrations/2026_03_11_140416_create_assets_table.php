<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('bmn_number')->unique();
            $table->string('serial_number')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('room_id')->nullable();
            $table->enum('status_kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->timestamps();

            $table->index('mac_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
