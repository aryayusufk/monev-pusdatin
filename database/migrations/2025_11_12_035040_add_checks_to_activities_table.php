<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Tambahkan constraint agar scope wajib diisi hanya jika dipilih untuk triwulan
        DB::statement("
            ALTER TABLE activities
            ADD CONSTRAINT chk_selected_quarterly_scope
            CHECK (
              (is_selected_quarterly = false AND selected_quarterly_scope IS NULL)
              OR (is_selected_quarterly = true  AND selected_quarterly_scope IS NOT NULL)
            )
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE activities DROP CONSTRAINT IF EXISTS chk_selected_quarterly_scope");
    }
};
