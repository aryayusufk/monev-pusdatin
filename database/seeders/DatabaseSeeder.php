<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
            //BidangSeeder::class,
            //IkuSeeder::class,
            //IkuBidangSeeder::class,
            UsersSeeder::class,
        ]);
    }
}
