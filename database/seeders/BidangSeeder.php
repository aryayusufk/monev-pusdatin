<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BidangSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        DB::table('bidangs')->upsert([
            ['kode'=>'BDG-DTKTI','nama'=>'Bidang Data dan Tata Kelola Teknologi Informasi','created_at'=>$now,'updated_at'=>$now],
            ['kode'=>'BDG-IPD','nama'=>'Bidang Infrastruktur dan Pusat Data','created_at'=>$now,'updated_at'=>$now],
            ['kode'=>'BDG-SI','nama'=>'Bidang Sistem Informasi','created_at'=>$now,'updated_at'=>$now],
            ['kode'=>'BDG-KIS','nama'=>'Bidang Keamanan Informasi dan Siber','created_at'=>$now,'updated_at'=>$now],
            ['kode'=>'BDG-TU','nama'=>'Bagian Tata Usaha','created_at'=>$now,'updated_at'=>$now],
        ], ['kode'], ['nama','updated_at']);
    }
}
