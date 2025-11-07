<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BidangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bidangs')->insertOrIgnore([
            ['kode'=>'BDG-1','nama'=>'Bidang A','created_at'=>now(),'updated_at'=>now()],
            ['kode'=>'BDG-2','nama'=>'Bidang B','created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}
