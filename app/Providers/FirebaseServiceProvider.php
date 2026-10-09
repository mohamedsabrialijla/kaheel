<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
         $this->app->bind(ClientInterface::class, function () {
            return new Client();
        });

        // Firebase Factory
        $this->app->singleton(Factory::class, function ($app) {
            return (new Factory)
                ->withServiceAccount(config('services.firebase.credentials'))
                ->withProjectId(config('services.firebase.project_id'));
        });

        // Firebase Messaging
        $this->app->singleton('firebase.messaging', function ($app) {
            return $app->make(Factory::class)->createMessaging();
        });

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}