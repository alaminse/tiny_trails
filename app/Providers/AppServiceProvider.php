<?php

namespace App\Providers;

use App\Services\JimiCloudService;
use App\Services\TrackSolidService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->singleton(TrackSolidService::class, function ($app) {
            return new TrackSolidService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
