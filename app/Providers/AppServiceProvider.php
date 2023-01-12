<?php

namespace App\Providers;

use AmoCRM\Client\AmoCRMApiClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * AmoCRMApiClient
     * @return void
     */
    public function register()
    {

        $this->app->singleton(AmoCRMApiClient::class, function () {
            $clientId = config('services.client-id');
            $clientSecret = config('services.client-secret');
            $redirectUri = config('services.redirect-uri');

            return new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
