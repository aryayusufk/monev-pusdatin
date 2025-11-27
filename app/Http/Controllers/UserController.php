<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bidang;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user.
     */
    public function index(Request $request)
    {
        $acting = $request->user();
        $this->ensureCanManageUsers($acting);

        $users = User::with(['bidang', 'roles'])
            ->orderBy('name')
            ->paginate(20);

        return view('users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Form buat user baru.
     */
    public function create(Request $request)
    {
        $acting = $request->user();
        $this->ensureCanManageUsers($acting);

        $bidangs = Bidang::orderBy('nama')->get();
        $roles   = Role::orderBy('label')->get();

        return view('users.create', [
            'bidangs' => $bidangs,
            'roles'   => $roles,
        ]);
    }

    /**
     * Simpan user baru.
     * Jika role = user_bidang dan ada bidang_id -> otomatis buat monev_bidang jika belum ada.
     */
    public function store(Request $request)
    {
        $acting = $request->user();
        $this->ensureCanManageUsers($acting); // hanya super_admin

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'username'  => ['required', 'string', 'max:50', 'unique:users,username'],
            'password'  => ['required', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
            'bidang_id' => ['nullable', 'exists:bidangs,id'],
            // field "role" berisi nama role: user_bidang, monev_bidang, tim_monev, dll.
            'role'      => ['required', 'string', 'exists:roles,name'],
        ]);

        $roleName = $data['role'];
        unset($data['role']);

        $data['is_active'] = $data['is_active'] ?? true;
        $data['password']  = Hash::make($data['password']);

        // === 1) Buat user utama ===
        $user = User::create($data);

        $role = Role::where('name', $roleName)->firstOrFail();
        $user->roles()->sync([$role->id]);

        // === 2) Jika role = user_bidang, auto-buat monev_bidang untuk bidang yang sama (jika belum ada) ===
        if ($roleName === 'user_bidang' && $user->bidang_id) {

            // cari role monev_bidang
            $monevRole = Role::where('name', 'monev_bidang')->first();

            if ($monevRole) {
                // cek apakah SUDAH ada user monev_bidang di bidang tersebut
                $existingMonev = User::whereHas('roles', function ($q) use ($monevRole) {
                        $q->where('roles.id', $monevRole->id);
                    })
                    ->where('bidang_id', $user->bidang_id)
                    ->first();

                if (! $existingMonev) {
                    // belum ada -> buat baru
                    $bidang = $user->bidang;

                    // contoh username monev: mnbid_bdg-dtk
                    $baseUsername = 'mnbid_' . strtolower(str_replace('-', '_', $bidang->kode));
                    $monevUsername = $baseUsername;

                    $i = 1;
                    while (User::where('username', $monevUsername)->exists()) {
                        $monevUsername = $baseUsername . $i;
                        $i++;
                    }

                    $monevUser = User::create([
                        'name'      => 'Monev ' . $bidang->nama,
                        'username'  => $monevUsername,
                        'password'  => Hash::make('password'), // default, nanti bisa diubah
                        'bidang_id' => $user->bidang_id,
                        'is_active' => true,
                    ]);

                    $monevUser->roles()->sync([$monevRole->id]);
                }
            }
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    /**
     * Form edit user.
     */
    public function edit(Request $request, User $user)
    {
        $acting = $request->user();
        $this->ensureCanManageUsers($acting);

        $bidangs = Bidang::orderBy('nama')->get();
        $roles   = Role::orderBy('label')->get();
        $currentRole = $user->roles()->first();

        return view('users.edit', [
            'user'        => $user,
            'bidangs'     => $bidangs,
            'roles'       => $roles,
            'currentRole' => $currentRole,
        ]);
    }

    /**
     * Update user.
     * (Tidak auto-buat monev di sini, hanya di store saja sesuai idemu).
     */
    public function update(Request $request, User $user)
    {
        $acting = $request->user();
        $this->ensureCanManageUsers($acting);

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'username'  => ['required', 'string', 'max:50', 'unique:users,username,' . $user->id],
            'password'  => ['nullable', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
            'bidang_id' => ['nullable', 'exists:bidangs,id'],
            'role'      => ['required', 'string', 'exists:roles,name'],
        ]);

        $roleName = $data['role'];
        unset($data['role']);

        $data['is_active'] = $data['is_active'] ?? false;

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        $role = Role::where('name', $roleName)->firstOrFail();
        $user->roles()->sync([$role->id]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Hapus user.
     */
    public function destroy(Request $request, User $user)
    {
        $acting = $request->user();
        $this->ensureCanManageUsers($acting);

        // kalau pakai soft deletes:
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Hanya super_admin yang boleh kelola user.
     */
    private function ensureCanManageUsers(User $acting): void
    {
        if (! $acting->hasRole('super_admin')) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola user.');
        }
    }
}
