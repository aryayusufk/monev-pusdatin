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
        $roles = DB::table('roles')->pluck('id', 'name');

        /*
        |--------------------------------------------------------------------------
        | 1) SUPER ADMIN
        |--------------------------------------------------------------------------
        */
        $superAdmin = User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('Sa@12345!'),
                'is_active' => true,
                'bidang_id' => null,
            ]
        );

        $this->attachRoleIfMissing($superAdmin, $roles['super_admin']);

        /*
        |--------------------------------------------------------------------------
        | 2) USER LEVEL PUSAT (TANPA BIDANG)
        |--------------------------------------------------------------------------
        */
        $users = [
            [
                'username' => 'tim_monev',
                'name'     => 'Tim Monev',
                'role'     => 'tim_monev',
                'password' => 'Tm@12345!',
            ],
            [
                'username' => 'sekre',
                'name'     => 'Sekretaris Pimpinan',
                'role'     => 'sekre_pimpinan',
                'password' => 'Sk@12345!',
            ],
            [
                'username' => 'pimpinan',
                'name'     => 'Pimpinan',
                'role'     => 'pimpinan',
                'password' => 'Pm@12345!',
            ],
        ];

        foreach ($users as $u) {
            $user = User::updateOrCreate(
                ['username' => $u['username']],
                [
                    'name'      => $u['name'],
                    'password'  => Hash::make($u['password']),
                    'is_active' => true,
                    'bidang_id' => null,
                ]
            );

            $this->attachRoleIfMissing($user, $roles[$u['role']]);
        }

    }

    private function attachRoleIfMissing(User $user, int $roleId): void
    {
        if (! $user->roles()->where('role_id', $roleId)->exists()) {
            $user->roles()->attach($roleId);
        }
    }
}
