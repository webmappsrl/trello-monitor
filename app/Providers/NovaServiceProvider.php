<?php

namespace App\Providers;

use App\Models\TrelloCard;
use App\Models\TrelloMember;
use App\Models\User;
use App\Nova\Metrics\CardDoneCount;
use App\Nova\Metrics\CardProgressCount;
use App\Nova\Metrics\CardRejectedCount;
use App\Nova\Metrics\CardToBeTestedCount;
use App\Nova\Metrics\CardTodayCount;
use App\Nova\Metrics\CardTomorrowCount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
        $userRequestId = Auth::id();
        $user = User::find($userRequestId);

        $userId = TrelloMember::where('name',$user->name)->first();

        $didDoYesterday = TrelloCard::where('member_id',$userId->id)->whereDate('created_at','<=', Carbon::now()->subDay())->whereDate('created_at','>=', Carbon::now()->subDay(2))->get();

        $problemsHavEncountered = TrelloCard::where('member_id',$userId->id)->whereDate('created_at','<=', Carbon::now()->subDay())->whereDate('created_at','>=', Carbon::now()->subDay(2))->get();
//dd($problemsHavEncountered);
        $toDoToday = TrelloCard::where('member_id',$userId->id)->whereDate('created_at', Carbon::now())->get();


        $header = collect(['Name', 'TrelloList', 'TrelloMember','Estimate','Customer','Total Time','Is_Archived','Created_at', 'Updated_at']);

        return [
//            new Help,
        new CardTomorrowCount(),
            new CardTodayCount,
            new CardProgressCount,
            new CardToBeTestedCount,
            new CardRejectedCount,
            new CardDoneCount,
            (new \Mako\CustomTableCard\CustomTableCard)
                ->header([
                    new \Mako\CustomTableCard\Table\Cell('Name'),
                    new \Mako\CustomTableCard\Table\Cell('TrelloList'),
                    new \Mako\CustomTableCard\Table\Cell('TrelloMember'),
                    new \Mako\CustomTableCard\Table\Cell('Estimate'),
                    new \Mako\CustomTableCard\Table\Cell('Customer'),
                    new \Mako\CustomTableCard\Table\Cell('Total Time'),
                    new \Mako\CustomTableCard\Table\Cell('Is_Archived'),
                    new \Mako\CustomTableCard\Table\Cell('Created_at'),
                    new \Mako\CustomTableCard\Table\Cell('Updated_at'),
                ])
                ->data($didDoYesterday->map(function ($order)
                {
                    return (new \Mako\CustomTableCard\Table\Row(
                        new \Mako\CustomTableCard\Table\Cell($order['name']),
                        new \Mako\CustomTableCard\Table\Cell($order['list_id']),
                        new \Mako\CustomTableCard\Table\Cell($order['member_id']),
                        new \Mako\CustomTableCard\Table\Cell($order['estimate']),
                        new \Mako\CustomTableCard\Table\Cell($order['customer']),
                        new \Mako\CustomTableCard\Table\Cell($order['total_time']),
                        new \Mako\CustomTableCard\Table\Cell($order['is_archived']),
                        new \Mako\CustomTableCard\Table\Cell($order['created_at']),
                        new \Mako\CustomTableCard\Table\Cell($order['updated_at']),
                    ))->viewLink('/resources/trello-cards/'.$order['id']);

                })->toArray())
                ->title('Che cosa ho fatto ieri?'),
            (new \Mako\CustomTableCard\CustomTableCard)
                ->header([
                    new \Mako\CustomTableCard\Table\Cell('Name'),
                    new \Mako\CustomTableCard\Table\Cell('TrelloList'),
                    new \Mako\CustomTableCard\Table\Cell('TrelloMember'),
                    new \Mako\CustomTableCard\Table\Cell('Estimate'),
                    new \Mako\CustomTableCard\Table\Cell('Customer'),
                    new \Mako\CustomTableCard\Table\Cell('Total Time'),
                    new \Mako\CustomTableCard\Table\Cell('Is_Archived'),
                    new \Mako\CustomTableCard\Table\Cell('Created_at'),
                    new \Mako\CustomTableCard\Table\Cell('Updated_at'),
                ])
                ->data($toDoToday->map(function ($order)
                {
                    return (new \Mako\CustomTableCard\Table\Row(
                        new \Mako\CustomTableCard\Table\Cell($order['name']),
                        new \Mako\CustomTableCard\Table\Cell($order['list_id']),
                        new \Mako\CustomTableCard\Table\Cell($order['member_id']),
                        new \Mako\CustomTableCard\Table\Cell($order['estimate']),
                        new \Mako\CustomTableCard\Table\Cell($order['customer']),
                        new \Mako\CustomTableCard\Table\Cell($order['total_time']),
                        new \Mako\CustomTableCard\Table\Cell($order['is_archived']),
                        new \Mako\CustomTableCard\Table\Cell($order['created_at']),
                        new \Mako\CustomTableCard\Table\Cell($order['updated_at']),
                    ))->viewLink('/resources/trello-cards/'.$order['id']);

                })->toArray())
                ->title('Che cosa farò oggi?'),
            (new \Mako\CustomTableCard\CustomTableCard)
                ->header([
                    new \Mako\CustomTableCard\Table\Cell('Name'),
                    new \Mako\CustomTableCard\Table\Cell('TrelloList'),
                    new \Mako\CustomTableCard\Table\Cell('TrelloMember'),
                    new \Mako\CustomTableCard\Table\Cell('Estimate'),
                    new \Mako\CustomTableCard\Table\Cell('Customer'),
                    new \Mako\CustomTableCard\Table\Cell('Total Time'),
                    new \Mako\CustomTableCard\Table\Cell('Is_Archived'),
                    new \Mako\CustomTableCard\Table\Cell('Created_at'),
                    new \Mako\CustomTableCard\Table\Cell('Updated_at'),
                ])
                ->data($toDoToday->map(function ($order)
                {
                    return (new \Mako\CustomTableCard\Table\Row(
                        new \Mako\CustomTableCard\Table\Cell($order['name']),
                        new \Mako\CustomTableCard\Table\Cell($order['list_id']),
                        new \Mako\CustomTableCard\Table\Cell($order['member_id']),
                        new \Mako\CustomTableCard\Table\Cell($order['estimate']),
                        new \Mako\CustomTableCard\Table\Cell($order['customer']),
                        new \Mako\CustomTableCard\Table\Cell($order['total_time']),
                        new \Mako\CustomTableCard\Table\Cell($order['is_archived']),
                        new \Mako\CustomTableCard\Table\Cell($order['created_at']),
                        new \Mako\CustomTableCard\Table\Cell($order['updated_at']),
                    ))->viewLink('/resources/trello-cards/'.$order['id']);

                })->toArray())
                ->title('Che problemi ho riscontrato?')
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
