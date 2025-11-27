<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IkuSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        DB::table('ikus')->upsert([
            ['kode'=>'IKU-01','nama'=>'Peningkatan Kualitas Data','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['kode'=>'IKU-02','nama'=>'Keandalan Infrastruktur TI','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['kode'=>'IKU-03','nama'=>'Layanan Sistem Informasi Tepat Waktu','is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
        ], ['kode'], ['nama','is_active','updated_at']);
    }
}
