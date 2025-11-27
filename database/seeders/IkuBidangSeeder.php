<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IkuBidangSeeder extends Seeder
{
    public function run(): void
    {
        $iku = DB::table('ikus')->pluck('id','kode');
        $bdg = DB::table('bidangs')->pluck('id','kode');

        $rows = [
            // IKU-01
            ['iku_id'=>$iku['IKU-01'],'bidang_id'=>$bdg['BDG-DTKTI']],
            ['iku_id'=>$iku['IKU-01'],'bidang_id'=>$bdg['BDG-SI']],
            // IKU-02
            ['iku_id'=>$iku['IKU-02'],'bidang_id'=>$bdg['BDG-IPD']],
            ['iku_id'=>$iku['IKU-02'],'bidang_id'=>$bdg['BDG-KIS']],
            // IKU-03
            ['iku_id'=>$iku['IKU-03'],'bidang_id'=>$bdg['BDG-SI']],
            ['iku_id'=>$iku['IKU-03'],'bidang_id'=>$bdg['BDG-TU']],
        ];

        DB::table('iku_bidang')->upsert($rows, ['iku_id','bidang_id'], []);
    }
}
