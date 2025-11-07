<?php
// database/migrations/2025_01_01_000320_create_activity_audits_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activity_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // created, updated, status_changed, selected_quarterly, file_uploaded, etc.
            $table->jsonb('changes')->nullable(); // diff ringkas
            $table->timestamps();
            $table->index(['activity_id']);
            $table->index(['action']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('activity_audits');
    }
};
