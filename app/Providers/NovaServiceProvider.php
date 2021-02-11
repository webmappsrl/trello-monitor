<?php

namespace App\Providers;

use App\Nova\Metrics\CardDoneCount;
use App\Nova\Metrics\CardProgressCount;
use App\Nova\Metrics\CardRejectedCount;
use App\Nova\Metrics\CardToBeTestedCount;
use App\Nova\Metrics\CardTodayCount;
use App\Nova\Metrics\CardTomorrowCount;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Nova::style('field-text',public_path('css/fieldText.css'));
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                'alessiopiccioli@webmapp.it',
                'gianmarcogagliardi@webmapp.it',
                'davidepizzato@webmapp.it',
                'pedramkatanchi@webmapp.it'
            ]);
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
//            new Help,
        new CardTomorrowCount(),
            new CardTodayCount,
            new CardProgressCount,
            new CardToBeTestedCount,
            new CardRejectedCount,
            new CardDoneCount,
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
//            new \Guratr\CommandRunner\CommandRunner,
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
