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
        //bind cards
        $this->app->bind('TrelloCardsService', function ($app) {
            return new \App\Services\TrelloCardsService($app->make('\App\Services\Api\TrelloCardsAPIService'));
        });

        //bind card
        $this->app->bind('TrelloCardService', function ($app) {
            return new \App\Services\TrelloCardService($app->make('\App\Services\Api\TrelloCardAPIService'));
        });

        //bind list
        $this->app->bind('TrelloListService', function ($app) {
            return new \App\Services\TrelloListService($app->make('\App\Services\Api\TrelloListAPIService'));
        });

        //bind member
        $this->app->bind('TrelloMemberService', function ($app) {
            return new \App\Services\TrelloMemberService($app->make('\App\Services\Api\TrelloMemberAPIService'));
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
