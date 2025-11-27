<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $roles   = DB::table('roles')->pluck('id', 'name');   // ['super_admin' => 1, ...]
        $bidangs = DB::table('bidangs')->pluck('id', 'kode'); // ['BDG-DTKTI' => 1, ...]

        // === 1) SUPER ADMIN ===
        $sa = User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password'),
                'is_active' => true,
                'bidang_id' => null,
            ]
        );
        $this->attachRoleIfMissing($sa, $roles['super_admin']);

        // === 2) USER LEVEL PUSAT (TIDAK TERGANTUNG BIDANG) ===
        $users = [
            [
                'username' => 'tim_monev',
                'name'     => 'Tim Monev',
                'role'     => 'tim_monev',
            ],
            [
                'username' => 'sekre',
                'name'     => 'Sekretaris Pimpinan',
                'role'     => 'sekre_pimpinan',
            ],
            [
                'username' => 'pimpinan',
                'name'     => 'Pimpinan',
                'role'     => 'pimpinan',
            ],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['username' => $u['username']],
                [
                    'name'      => $u['name'],
                    'password'  => Hash::make('password'),
                    'is_active' => true,
                    'bidang_id' => null,
                ]
            );

            $this->attachRoleIfMissing($user, $roles[$u['role']]);
        }

        // ❌ TIDAK membuat user_bidang dan monev_bidang di seeder.
        // Mereka akan dibuat lewat UI oleh superadmin.
    }

    private function attachRoleIfMissing(User $u, $roleId): void
    {
        if (! $u->roles()->where('role_id', $roleId)->exists()) {
            $u->roles()->attach($roleId);
        }
    }
}
