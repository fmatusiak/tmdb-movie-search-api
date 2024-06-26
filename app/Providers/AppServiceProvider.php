<?php

namespace App\Providers;

use App\Services\TMDBApi;
use App\Services\TMDBClient;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {
            return new Client();
        });

        $this->app->singleton(TMDBClient::class, function ($app) {
            return new TMDBClient($app->make(Client::class));
        });

        $this->app->singleton(TMDBApi::class, function ($app) {
            return new TMDBApi($app->make(TMDBClient::class));
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
