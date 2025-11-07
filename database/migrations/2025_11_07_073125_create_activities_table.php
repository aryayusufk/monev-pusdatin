<?php
// database/migrations/2025_01_01_000200_create_activities_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bidang_id')->constrained('bidangs')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->date('occurred_at'); // tanggal kegiatan terjadi (acuan periode)
            $table->string('lokasi')->nullable();

            // Denormalized period fields for grouping & reporting
            $table->integer('year');        // ex: 2025
            $table->tinyInteger('iso_week'); // 1-53 (ISO week)
            $table->tinyInteger('month');    // 1-12
            $table->tinyInteger('quarter');  // 1-4

            // Status siklus laporan
            $table->enum('status', ['draft','submitted','reviewed','approved','rejected'])->default('draft');

            // Khusus penyortiran triwulan (dipilih manual oleh role terkait)
            $table->boolean('is_selected_quarterly')->default(false);
            $table->enum('selected_quarterly_scope', ['bidang','global'])->nullable();
            $table->foreignId('selected_quarterly_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('selected_quarterly_at')->nullable();

            $table->jsonb('extra')->nullable(); // ruang fleksibel: KPI nilai, narasi tambahan, dsb.

            $table->timestamps();
            $table->softDeletes();

            $table->index(['bidang_id','year']);
            $table->index(['year','iso_week']);
            $table->index(['year','month']);
            $table->index(['year','quarter']);
            $table->index(['status']);
            $table->index(['is_selected_quarterly','selected_quarterly_scope']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('activities');
    }
};
