<?php

namespace App\Providers;

use App\Observers\RideObserver;
use Illuminate\Support\ServiceProvider;
use Modules\RideAssignment\app\Models\Ride;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Add this binding to your existing AppServiceProvider

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Ride::observe(RideObserver::class);
    }
}
