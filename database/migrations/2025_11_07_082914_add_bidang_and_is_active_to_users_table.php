<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // tambahkan kalau belum ada
            if (!Schema::hasColumn('users', 'bidang_id')) {
                $table->foreignId('bidang_id')->nullable()
                    ->after('password')
                    ->constrained('bidangs')->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('bidang_id');
            }

            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }

            $table->index(['bidang_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // hapus index kalau ada
            try { $table->dropIndex(['users_bidang_id_is_active_index']); } catch (\Throwable $e) {}

            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
            if (Schema::hasColumn('users', 'bidang_id')) {
                $table->dropConstrainedForeignId('bidang_id');
            }
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
