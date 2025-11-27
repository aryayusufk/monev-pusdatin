<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('activity_id')
                ->constrained()
                ->cascadeOnDelete();

            // kolom path untuk menyimpan lokasi file di storage
            $table->string('path');

            // ➕ tambahkan kolom-kolom ini:
            $table->string('original_name');          // nama file asli (BAKEKOK.pdf)
            $table->string('mime_type')->nullable();  // tipe file (application/pdf)
            $table->unsignedBigInteger('size')->nullable(); // ukuran file (byte)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_files');
    }
};
