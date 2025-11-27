<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('export_templates', function (Blueprint $t) {
            $t->id();
            $t->string('name'); // nama template (mis: "Triwulan v1")
            $t->enum('period_type', ['weekly','monthly','quarterly','yearly']);
            $t->enum('scope', ['bidang','global']); // template untuk per-bidang atau agregat global
            $t->string('version')->nullable();      // versi template jika ada
            $t->string('path');                     // lokasi file template (storage path)
            $t->jsonb('settings')->nullable();      // pengaturan render (mis. mapping field)

            $t->timestamps();
            $t->softDeletes();

            // hindari duplikasi template
            $t->unique(['period_type','scope','name','version'], 'export_templates_unique_combo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_templates');
    }
};
