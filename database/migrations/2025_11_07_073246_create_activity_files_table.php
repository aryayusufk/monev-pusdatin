<?php
// database/migrations/2025_01_01_000300_create_activity_files_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activity_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->string('filename');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('disk')->default('public'); // Laravel storage disk
            $table->string('path'); // relative path di storage
            $table->timestamps();
            $table->softDeletes();
            $table->index(['activity_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('activity_files');
    }
};
