<?php
// database/migrations/2025_01_01_000310_create_activity_notes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activity_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('note');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['activity_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('activity_notes');
    }
};
