<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\Iku;
use App\Policies\ActivityPolicy;
use App\Policies\IkuPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Activity::class => ActivityPolicy::class,
        Iku::class      => IkuPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Laporan Bidang (lihat)
        Gate::define('reports.bidang.view', function ($user) {
            return $user->hasRole([
                'super_admin',
                'tim_monev',
                'sekre_pimpinan',
                'monev_bidang',
                'user_bidang',
                'pimpinan',
            ]);
        });

        // Laporan Monitoring & Evaluasi (lihat)
        Gate::define('reports.monev.view', function ($user) {
            return $user->hasRole([
                'super_admin',
                'tim_monev',
                'sekre_pimpinan',
                'monev_bidang',
            ]);
        });

        // Sortir (kirim/hapus ke Monev + select quarterly)
        Gate::define('reports.sort', function ($user) {
            return $user->hasRole([
                'super_admin',
                'tim_monev',
                'sekre_pimpinan',
                'monev_bidang',
                'user_bidang',
            ]);
        });

        // Buat Laporan
        Gate::define('activities.create', function ($user) {
            return $user->hasRole([
                'super_admin',
                'tim_monev',
                'sekre_pimpinan',
                'monev_bidang',
                'user_bidang',
            ]);
        });

        // Catatan Aktivitas (log)
        Gate::define('activitylog.view', function ($user) {
            return $user->hasRole([
                'super_admin',
                'tim_monev',
                'sekre_pimpinan',
            ]);
        });

        // Konfigurasi: IKU (super_admin, tim_monev, sekre_pimpinan)
        Gate::define('config.iku', function ($user) {
            return $user->hasRole([
                'super_admin',
                'tim_monev',
                'sekre_pimpinan',
            ]);
        });

        // Konfigurasi: Berita (super_admin, tim_monev, sekre_pimpinan)
        Gate::define('config.news', function ($user) {
            return $user->hasRole([
                'super_admin',
                'tim_monev',
                'sekre_pimpinan',
            ]);
        });

        // Konfigurasi: Users (super_admin saja)
        Gate::define('config.users', function ($user) {
            return $user->hasRole('super_admin');
        });

        // Konfigurasi: Bidang (super_admin saja)
        Gate::define('config.bidang', function ($user) {
            return $user->hasRole('super_admin');
        });
    }
}
