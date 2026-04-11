<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pc_reports', function (Blueprint $table) {
            $table->bigInteger('total_ram_kb')->nullable()->after('os_build');
            $table->bigInteger('total_disk_b')->nullable()->after('disk_free_b');
            $table->string('disk_status')->nullable()->after('total_disk_b');
            $table->string('last_patch')->nullable()->after('disk_status');
            $table->boolean('is_trouble')->default(false)->after('last_patch');
            $table->text('trouble_note')->nullable()->after('is_trouble');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pc_reports', function (Blueprint $table) {
            $table->dropColumn([
                'total_ram_kb',
                'total_disk_b',
                'disk_status',
                'last_patch',
                'is_trouble',
                'trouble_note'
            ]);
        });
    }
};