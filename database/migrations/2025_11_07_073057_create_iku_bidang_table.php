<?php
// database/migrations/2025_01_01_000110_create_iku_bidang_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('iku_bidang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iku_id')->constrained('ikus')->cascadeOnDelete();
            $table->foreignId('bidang_id')->constrained('bidangs')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['iku_id','bidang_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('iku_bidang');
    }
};
