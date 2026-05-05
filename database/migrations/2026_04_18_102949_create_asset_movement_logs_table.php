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
        Schema::create('asset_movement_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignId('old_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('new_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('old_room_id')->nullable()->constrained('rooms')->onDelete('set null');
            $table->foreignId('new_room_id')->nullable()->constrained('rooms')->onDelete('set null');
            $table->string('action_type')->comment('ownership_change, room_change, or both');
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_movement_logs');
    }
};
