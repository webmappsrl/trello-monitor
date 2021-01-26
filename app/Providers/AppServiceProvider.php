<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('TrelloCardsService', function ($app) {
            return new \App\Services\TrelloCardsService($app->make('\App\Services\Api\TrelloCardsAPIService'));
        });

        $this->app->bind('TrelloCardService', function ($app) {
            return new \App\Services\TrelloCardService($app->make('\App\Services\Api\TrelloCardAPIService'));
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
