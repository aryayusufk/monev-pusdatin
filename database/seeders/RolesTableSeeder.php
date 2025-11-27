<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        DB::table('roles')->upsert([
            ['name' => 'super_admin',   'label' => 'Super Admin',            'created_at'=>$now, 'updated_at'=>$now],
            ['name' => 'tim_monev',     'label' => 'Tim Monev',              'created_at'=>$now, 'updated_at'=>$now],
            ['name' => 'sekre_pimpinan','label' => 'Sekretaris Pimpinan',    'created_at'=>$now, 'updated_at'=>$now],
            ['name' => 'pimpinan',      'label' => 'Pimpinan',               'created_at'=>$now, 'updated_at'=>$now],
            ['name' => 'monev_bidang',  'label' => 'Monev Bidang',           'created_at'=>$now, 'updated_at'=>$now],
            ['name' => 'user_bidang',   'label' => 'User Bidang',            'created_at'=>$now, 'updated_at'=>$now],
        ], ['name'], ['label','updated_at']);
    }
}
