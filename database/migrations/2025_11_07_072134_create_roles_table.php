<?php
// database/migrations/2025_01_01_000000_create_roles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // super_admin, tim_monev, sekre_pimpinan, pimpinan, monev_bidang, user_bidang
            $table->string('label')->nullable(); // Friendly label
            $table->timestamps();
        });

        // Seed minimal roles (opsional—bisa juga pakai seeder terpisah)
        // DB::table('roles')->insert([...]);
    }
    public function down(): void {
        Schema::dropIfExists('roles');
    }
};
