<?php
// database/migrations/2025_01_01_000210_create_activity_iku_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activity_iku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->foreignId('iku_id')->constrained('ikus')->cascadeOnDelete();
            // optional bobot/kontribusi jika 1 activity mendukung >1 IKU
            $table->decimal('contribution', 5, 2)->nullable(); // 0-100%
            $table->timestamps();
            $table->unique(['activity_id','iku_id']);
            $table->index(['iku_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('activity_iku');
    }
};
