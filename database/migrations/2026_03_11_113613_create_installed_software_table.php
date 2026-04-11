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
        Schema::create('installed_software', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pc_report_id')->constrained()->onDelete('cascade');
            $table->string('software_name');
            $table->string('software_version')->nullable();
            $table->string('software_publisher')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installed_software');
    }
};
