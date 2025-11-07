<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->upsert([
            ['name'=>'super_admin','label'=>'Super Admin'],
            ['name'=>'tim_monev','label'=>'Tim Monev'],
            ['name'=>'sekre_pimpinan','label'=>'Sekretaris Pimpinan'],
            ['name'=>'pimpinan','label'=>'Pimpinan'],
            ['name'=>'monev_bidang','label'=>'Monev Bidang'],
            ['name'=>'user_bidang','label'=>'User Bidang'],
        ], ['name'], ['label']);
    }
}
