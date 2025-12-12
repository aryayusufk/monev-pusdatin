<?php

namespace App\Policies;

use App\Models\Iku;
use App\Models\User;

class IkuPolicy
{
    /**
     * Siapa saja yang boleh MELIHAT daftar IKU.
     * Di sini semua role yang login boleh lihat.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasRole('tim_monev')
            || $user->hasRole('sekre_pimpinan')
            || $user->hasRole('pimpinan')
            || $user->hasRole('monev_bidang')
            || $user->hasRole('user_bidang');
    }

    /**
     * Ability "manage" dipakai di IkuController dengan Gate::authorize('manage', Iku::class)
     *
     * Di sini kita izinkan HANYA:
     * - super_admin
     * - tim_monev
     * - sekre_pimpinan
     */
    public function manage(User $user): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasRole('tim_monev')
            || $user->hasRole('sekre_pimpinan');
    }

    public function delete(User $user, Iku $iku): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasRole('tim_monev')
            || $user->hasRole('sekre_pimpinan');
    }

    /**
     * Kalau suatu saat kamu pakai $this->authorize('view', $iku)
     */
    public function view(User $user, Iku $iku): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->manage($user);
    }

    public function update(User $user, Iku $iku): bool
    {
        return $this->manage($user);
    }

    public function toggleActive(User $user, Iku $iku): bool
    {
        return $this->manage($user);
    }
}