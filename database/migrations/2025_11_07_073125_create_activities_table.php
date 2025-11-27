<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $t) {
            $t->id();

            // konteks organisasi & pembuat
            $t->foreignId('bidang_id')->constrained('bidangs')->cascadeOnDelete();
            $t->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // tanggal & periodisasi
            $t->date('occurred_at');
            $t->integer('year');          // contoh: 2025
            $t->tinyInteger('month');     // 1..12
            $t->tinyInteger('quarter');   // 1..4
            $t->integer('iso_week');      // 1..53

            // ====== ISI LAPORAN (tambahan yang kamu minta) ======
            $t->longText('kegiatan')->nullable();
            $t->longText('tindak_lanjut')->nullable();
            $t->longText('kendala')->nullable();
            $t->longText('percepatan')->nullable();
            $t->longText('proyeksi_kedepan')->nullable();

            // status siklus & seleksi triwulan
            $t->enum('status', ['draft','submitted','archived'])->default('submitted');
            $t->boolean('is_selected_quarterly')->default(false);
            $t->enum('selected_quarterly_scope', ['bidang','global'])->nullable();
            $t->foreignId('selected_quarterly_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestampTz('selected_quarterly_at')->nullable();

            // metadata bebas kalau perlu
            $t->jsonb('metadata')->nullable();

            $t->timestamps();
            $t->softDeletes();

            // index umum
            $t->index(['year','month']);
            $t->index(['year','quarter']);
            $t->index(['year','iso_week']);
            $t->index(['bidang_id','occurred_at']);

            // index untuk query triwulan terpilih
            $t->index(['is_selected_quarterly','year','quarter','bidang_id'], 'activities_quarterly_idx');
        });

        // CHECK constraints (PostgreSQL)
        DB::statement("ALTER TABLE activities ADD CONSTRAINT chk_act_month   CHECK (month BETWEEN 1 AND 12)");
        DB::statement("ALTER TABLE activities ADD CONSTRAINT chk_act_quarter CHECK (quarter BETWEEN 1 AND 4)");
        DB::statement("ALTER TABLE activities ADD CONSTRAINT chk_act_week    CHECK (iso_week BETWEEN 1 AND 53)");
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
