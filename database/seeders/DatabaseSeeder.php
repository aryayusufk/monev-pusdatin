<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan seeder dasar
        $this->call([
            RolesTableSeeder::class,
            BidangSeeder::class,
        ]);

        // (Opsional) Buat 1 akun admin untuk login awal
        $admin = User::firstOrCreate(
            ['username' => 'superadmin1'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // Assign role super_admin ke akun ini
        $roleId = \DB::table('roles')->where('name', 'super_admin')->value('id');
        if ($roleId && !$admin->roles()->where('role_id', $roleId)->exists()) {
            $admin->roles()->attach($roleId);
        }

        // (Opsional) Factory user tambahan untuk testing
        // User::factory(5)->create();
    }
}
