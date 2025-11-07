<?php
// database/migrations/2025_01_01_000400_create_export_templates_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('export_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('period_type', ['weekly','monthly','quarterly','yearly']);
            $table->enum('scope', ['bidang','global'])->default('bidang'); // template per-bidang atau agregat global
            $table->string('name');
            $table->string('version')->nullable();
            $table->string('disk')->default('public');
            $table->string('path'); // path file template (docx/xlsx, dll.)
            $table->boolean('is_active')->default(true);
            $table->jsonb('settings')->nullable(); // mapping field → placeholder, opsi header/footer, dsb.
            $table->timestamps();
            $table->softDeletes();
            $table->index(['period_type','scope','is_active']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('export_templates');
    }
};
