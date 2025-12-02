<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Iku;
use App\Models\User;

class ConfigurationController extends Controller
{
    public function index()
    {

        // Tabel IKU
        $ikus = Iku::with('bidangs')
            ->orderBy('kode')
            ->limit(5)
            ->get();

        $ikuColumns = [
            'Nama IKU',
            'Target',
            'Deskripsi',
            'Bidang Terkait',
        ];

        $ikuRows = $ikus->map(function ($iku) {
            return [
                'id'         => $iku->id,
                'nama'       => $iku->nama,
                'target'     => $iku->target ?? '-',
                'deskripsi'  => $iku->deskripsi ?? '-',
                'bidangs'    => $iku->bidangs->pluck('nama')->join(', ') ?: '-',
            ];
        });

        //Tabel User
        $users = User::with(['bidang', 'roles'])
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(5)
            ->get();

        $userColumns = [
            'Nama',
            'Username',
            'Bidang',
            'Role',
        ];

        $userRows = $users->map(function ($user) {
            return [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'bidang'   => optional($user->bidang)->nama ?? '-',
                'role'     => $user->roles->pluck('name')->join(', ') ?: '-',
            ];
        });

        return view('dashboard', compact('ikuColumns', 'ikuRows', 'userColumns', 'userRows'));
    }
}
