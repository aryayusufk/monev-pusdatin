<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BidangController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $this->ensureCanManageBidangs($user);

        $bidangs = Bidang::orderBy('kode')->paginate(20);

        $columns = [
            'Kode Bidang',
            'Nama Bidang',
            'Deskripsi',
            'Akun',
        ];

        $rows = $bidangs->getCollection()->map(function (Bidang $bidang) {
            return [
                // dipakai untuk checkbox / action di komponen tabel
                'id'      => $bidang->id,

                // Kolom 1: Kode Bidang
                'kode'    => $bidang->kode,

                // Kolom 2: Nama Bidang
                'nama'    => $bidang->nama,

                // Kolom 3: Deskripsi
                'deskripsi' => $bidang->deskripsi ?? '-',


                // Kolom 4: Akun (jumlah user bidang)
                'akun'    => $bidang->users()->count(),
            ];
        });

        return view('bidangs.index', [
            'bidangs' => $bidangs,
            'columns' => $columns,
            'rows'    => $rows,
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $this->ensureCanManageBidangs($user);

        return view('bidangs.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $this->ensureCanManageBidangs($user);

        $data = $request->validate([
            'nama_bidang' => ['required', 'string', 'max:255'],
            'username'    => ['required', 'string', 'max:50', 'unique:users,username'],
            'password'    => ['required', 'string', 'min:6'],
        ]);

        // Hash password sekali → dipakai user bidang & monev bidang
        $hashedPassword = Hash::make($data['password']);

        // Generate kode bidang
        $kode = 'BDG_' . strtoupper(Str::slug($data['nama_bidang'], '_'));
        $baseKode = $kode;
        $i = 1;

        while (Bidang::where('kode', $kode)->exists()) {
            $kode = $baseKode . '_' . $i;
            $i++;
        }

        // Buat bidang
        $bidang = Bidang::create([
            'kode'      => $kode,
            'nama'      => $data['nama_bidang'],
            'deskripsi' => null,
        ]);

        // Buat User Bidang
        $userBidang = User::create([
            'name'      => 'User ' . $bidang->nama,
            'username'  => $data['username'],
            'password'  => $hashedPassword,
            'bidang_id' => $bidang->id,
            'is_active' => true,
        ]);

        $roleUserBidang = Role::where('name', 'user_bidang')->firstOrFail();
        $userBidang->roles()->sync([$roleUserBidang->id]);

        // Auto buat Monev Bidang
        $roleMonev = Role::where('name', 'monev_bidang')->first();
        if ($roleMonev) {

            $slugNamaBidang = strtolower(Str::slug($bidang->nama, '_'));
            $baseUsername   = 'monev_' . $slugNamaBidang;
            
            $monevUsername = $baseUsername;

            $i = 1;
            while (User::where('username', $monevUsername)->exists()) {
                $monevUsername = $baseUsername . $i;
                $i++;
            }

            $monevUser = User::create([
                'name'      => 'Monev ' . $bidang->nama,
                'username'  => $monevUsername,
                'password'  => $hashedPassword, // sama dengan user bidang
                'bidang_id' => $bidang->id,
                'is_active' => true,
            ]);

            $monevUser->roles()->sync([$roleMonev->id]);
        }

        return redirect()
            ->route('bidangs.index')
            ->with('success', 'Bidang baru beserta User Bidang & Monev Bidang berhasil dibuat.');
    }

    public function edit(Request $request, Bidang $bidang)
    {
        $user = $request->user();
        $this->ensureCanManageBidangs($user);

        return view('bidangs.edit', compact('bidang'));
    }

    public function update(Request $request, Bidang $bidang)
    {
        $user = $request->user();
        $this->ensureCanManageBidangs($user);

        $data = $request->validate([
            'kode'      => ['required', 'string', 'max:50', 'unique:bidangs,kode,' . $bidang->id],
            'nama'      => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $bidang->update($data);

        return redirect()
            ->route('bidangs.index')
            ->with('success', 'Bidang berhasil diperbarui.');
    }

    public function destroy(Request $request, Bidang $bidang)
    {
        $user = $request->user();
        $this->ensureCanManageBidangs($user);

        // Blok jika masih ada laporan
        if ($bidang->activities()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus Bidang karena masih ada laporan kegiatan.');
        }

        // Blok jika masih dipakai IKU
        if ($bidang->ikus()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus Bidang karena masih dipakai oleh IKU.');
        }

        // Hapus semua user bidang
        foreach ($bidang->users as $u) {
            $u->delete(); // soft delete
        }

        // Hapus bidang
        $bidang->delete();

        return redirect()
            ->route('bidangs.index')
            ->with('success', 'Bidang dan seluruh user bidang berhasil dihapus.');
    }

    private function ensureCanManageBidangs(User $user): void
    {
        if (! $user->hasRole('super_admin')) {
            abort(403, 'Anda tidak berhak mengelola bidang.');
        }
    }
}
