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
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        Activity::class => ActivityPolicy::class,
        Iku::class      => IkuPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /*
        |--------------------------------------------------------------------------
        | Additional Gates (opsional)
        |--------------------------------------------------------------------------
        | Kamu bisa menambahkan Gate tambahan jika butuh.
        | Tapi karena kita pakai Policies, ini tidak wajib.
        |
        | Misalnya:
        | Gate::define('manage-users', fn($user) => $user->hasRole('super_admin'));
        |
        */
    }
}
