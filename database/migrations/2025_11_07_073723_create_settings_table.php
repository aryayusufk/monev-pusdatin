<?php
// database/migrations/2025_01_01_000500_create_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();      // e.g., 'reporting.iso_week_start', 'ui.brand_name'
            $table->jsonb('value')->nullable();   // fleksibel
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('settings');
    }
};
