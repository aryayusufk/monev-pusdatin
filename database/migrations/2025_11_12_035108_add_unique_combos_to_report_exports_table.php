<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // UNIQUE untuk scope=global (tanpa bidang_id)
        DB::statement("
            CREATE UNIQUE INDEX re_unique_global
            ON report_exports (period_type, year, COALESCE(month,0), COALESCE(quarter,0), COALESCE(iso_week,0), template_id)
            WHERE scope = 'global' AND bidang_id IS NULL AND deleted_at IS NULL
        ");

        // UNIQUE untuk scope=bidang (dengan bidang_id)
        DB::statement("
            CREATE UNIQUE INDEX re_unique_bidang
            ON report_exports (period_type, year, COALESCE(month,0), COALESCE(quarter,0), COALESCE(iso_week,0), bidang_id, template_id)
            WHERE scope = 'bidang' AND bidang_id IS NOT NULL AND deleted_at IS NULL
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS re_unique_global");
        DB::statement("DROP INDEX IF EXISTS re_unique_bidang");
    }
};
