<?php

namespace App\Observers;

use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ActivityObserver
{
    public function creating(Activity $activity): void
    {
        // 1) Tanggal otomatis: kalau belum diisi, set ke hari ini (Asia/Jakarta dari config)
        if (! $activity->occurred_at) {
            $activity->occurred_at = now();
        }

        // Pastikan $date adalah instance Carbon
        if ($activity->occurred_at instanceof Carbon) {
            $date = $activity->occurred_at;
        } else {
            $date = Carbon::parse($activity->occurred_at);
            $activity->occurred_at = $date;
        }

        // 2) Periodisasi otomatis
        $activity->year     = $date->year;
        $activity->month    = $date->month;
        $activity->quarter  = intdiv($date->month - 1, 3) + 1;
        $activity->iso_week = $date->isoWeek();

        // 3) Bidang & created_by otomatis dari user login
        if (Auth::check()) {
            if (! $activity->created_by) {
                $activity->created_by = Auth::id();
            }

            if (! $activity->bidang_id && Auth::user()->bidang_id) {
                $activity->bidang_id = Auth::user()->bidang_id;
            }
        }
    }

    public function updating(Activity $activity): void
    {
        // Kalau tanggal diubah saat edit, periodisasi ikut diupdate
        if ($activity->isDirty('occurred_at')) {
            $date = $activity->occurred_at instanceof Carbon
                ? $activity->occurred_at
                : Carbon::parse($activity->occurred_at);

            $activity->year     = $date->year;
            $activity->month    = $date->month;
            $activity->quarter  = intdiv($date->month - 1, 3) + 1;
            $activity->iso_week = $date->isoWeek();
        }
    }
}
