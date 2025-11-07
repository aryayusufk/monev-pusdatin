<?php
// database/migrations/2025_01_01_000100_create_ikus_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ikus', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // contoh: IKU-01
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('active_from')->nullable();
            $table->date('active_to')->nullable();
            $table->jsonb('metadata')->nullable(); // ruang untuk target, satuan, dsb.
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_active']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('ikus');
    }
};
