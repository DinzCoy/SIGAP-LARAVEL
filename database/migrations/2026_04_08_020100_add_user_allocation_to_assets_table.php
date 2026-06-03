<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('room_id')->constrained('users')->nullOnDelete();
            $table->date('allocated_at')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'allocated_at']);
        });
    }
};
