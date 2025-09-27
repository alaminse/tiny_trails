<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Messaging::class, function ($app) {
            $firebase = (new Factory)
                ->withServiceAccount(config('firebase'))
                ->withProjectId(config('firebase.project_id'));

            return $firebase->createMessaging();
        });
    }

    public function boot()
    {
        //
    }
}
