<?php
// database/migrations/2025_01_01_000410_create_report_exports_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('report_exports', function (Blueprint $table) {
            $table->id();
            $table->enum('period_type', ['weekly','monthly','quarterly','yearly']);
            $table->integer('year');
            $table->tinyInteger('iso_week')->nullable();
            $table->tinyInteger('month')->nullable();
            $table->tinyInteger('quarter')->nullable();

            $table->enum('scope', ['bidang','global'])->default('bidang');
            $table->foreignId('bidang_id')->nullable()->constrained('bidangs')->nullOnDelete();

            $table->foreignId('generated_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('export_templates')->nullOnDelete();

            $table->string('disk')->default('public');
            $table->string('path'); // path file hasil export
            $table->timestampTz('generated_at');

            $table->jsonb('parameters')->nullable(); // filter/opsi yang dipakai saat generate

            $table->timestamps();
            $table->softDeletes();

            $table->index(['period_type','year','iso_week','month','quarter']);
            $table->index(['scope','bidang_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('report_exports');
    }
};
