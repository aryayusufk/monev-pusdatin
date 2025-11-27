<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('report_exports', function (Blueprint $t) {
            $t->id();

            // Identitas periode & ruang lingkup
            $t->enum('period_type', ['weekly','monthly','quarterly','yearly']);
            $t->enum('scope', ['bidang','global']);
            $t->integer('year');
            $t->tinyInteger('month')->nullable();    // untuk monthly
            $t->tinyInteger('quarter')->nullable();  // untuk quarterly
            $t->integer('iso_week')->nullable();     // untuk weekly

            // jika scope = bidang, isi bidang_id; kalau global -> null
            $t->foreignId('bidang_id')->nullable()->constrained('bidangs')->nullOnDelete();

            // template yang digunakan
            $t->foreignId('template_id')->constrained('export_templates')->cascadeOnDelete();

            // siapa yang generate (nullable: tetap simpan arsip meski user dihapus)
            $t->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();

            $t->string('path');           // path file hasil ekspor
            $t->jsonb('params')->nullable();  // parameter yang dipakai saat render
            $t->timestampTz('generated_at')->useCurrent();

            $t->timestamps();
            $t->softDeletes();

            // indeks umum untuk pencarian cepat
            $t->index(['period_type','year']);
            $t->index(['period_type','year','quarter']);
            $t->index(['period_type','year','month']);
            $t->index(['period_type','year','iso_week']);
            $t->index(['scope','bidang_id']);
        });

        // CHECK constraints (PostgreSQL)
        DB::statement("ALTER TABLE report_exports ADD CONSTRAINT chk_re_month   CHECK (month IS NULL OR month BETWEEN 1 AND 12)");
        DB::statement("ALTER TABLE report_exports ADD CONSTRAINT chk_re_quarter CHECK (quarter IS NULL OR quarter BETWEEN 1 AND 4)");
        DB::statement("ALTER TABLE report_exports ADD CONSTRAINT chk_re_week    CHECK (iso_week IS NULL OR iso_week BETWEEN 1 AND 53)");
    }

    public function down(): void
    {
        Schema::dropIfExists('report_exports');
    }
};
