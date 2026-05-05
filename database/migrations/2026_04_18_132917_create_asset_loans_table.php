<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('borrower_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('loaned_at');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->enum('status', ['active', 'returned'])->default('active');
            $table->timestamps();

            // Hanya boleh 1 peminjaman aktif per aset
            $table->unique(['asset_id', 'status'], 'unique_active_loan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_loans');
    }
};
