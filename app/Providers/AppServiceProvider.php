<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Activity;
use App\Observers\ActivityObserver;
use App\Models\Bidang;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Activity::observe(ActivityObserver::class);

        View::composer('layouts.navigation', function ($view) {
            $view->with('navBidangs', Bidang::orderBy('nama')->get());
        });
    }
}
