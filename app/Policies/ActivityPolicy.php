<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->hasRole('super_admin')
            || $u->hasRole('tim_monev')
            || $u->hasRole('sekre_pimpinan')
            || $u->hasRole('pimpinan')
            || $u->hasRole('monev_bidang')
            || $u->hasRole('user_bidang');
    }

    public function view(User $u, Activity $a): bool
    {
        if ($u->hasRole('super_admin') || $u->hasRole('tim_monev') || $u->hasRole('sekre_pimpinan') || $u->hasRole('pimpinan')) {
            return true;
        }

        if ($u->hasRole('monev_bidang') || $u->hasRole('user_bidang')) {
            return $u->bidang_id === $a->bidang_id;
        }

        return false;
    }

    public function create(User $u): bool
    {
        return $u->hasRole('super_admin')
            || $u->hasRole('tim_monev')
            || $u->hasRole('sekre_pimpinan')
            || $u->hasRole('monev_bidang')
            || $u->hasRole('user_bidang');
    }

    public function update(User $u, Activity $a): bool
    {
        if ($u->hasRole('super_admin') || $u->hasRole('tim_monev') || $u->hasRole('sekre_pimpinan')) {
            return true;
        }

        if ($u->hasRole('monev_bidang') || $u->hasRole('user_bidang')) {
            return $u->bidang_id === $a->bidang_id;
        }

        return false;
    }

    public function delete(User $u, Activity $a): bool
    {
        return $this->update($u, $a);
    }

    public function selectQuarterly(User $u, Activity $a): bool
    {
        if ($u->hasRole('super_admin') || $u->hasRole('tim_monev') || $u->hasRole('sekre_pimpinan')) {
            return true;
        }

        if ($u->hasRole('monev_bidang')) {
            return $u->bidang_id === $a->bidang_id;
        }

        return false;
    }
}
