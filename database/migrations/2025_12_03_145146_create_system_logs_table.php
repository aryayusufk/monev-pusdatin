<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();

            // Pelaku (boleh null kalau suatu saat ada aksi tanpa login)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Kode aksi, misal: user.created, iku.created, activity.created
            $table->string('action', 100);

            // Deskripsi yang akan ditampilkan di FE
            $table->text('description');

            // created_at = timestamp aksi
            $table->timestamps();

            $table->index('action');
            $table->index(['user_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
