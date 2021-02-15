<?php

namespace App\Providers;

use App\Models\TrelloCard;
use App\Models\TrelloList;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Fields\Select;
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
//        dd($userId);

        $trelloListNot = TrelloList::whereIn('name',['CYCLANDO OPTIMIZE','BACKLOG'])->pluck('id');

        $trelloListOk = TrelloList::whereIn('name',['TODAY','PROGRESS','REJECTED'])->pluck('id');

        $didDoYesterday = DB::table('trello_cards')
            ->select('trello_cards.*','trello_members.name as member_name','trello_lists.name as list_name')
            ->join('trello_members', 'trello_cards.member_id', '<=', 'trello_members.id')
            ->join('trello_lists', 'trello_cards.list_id', '>', 'trello_lists.id')
            ->where('member_id',$userId->id)
            ->whereNotIn('trello_lists.id',  $trelloListNot)
            ->whereDate('last_activity','=', Carbon::yesterday())
            ->where('is_archived',0)
            ->get();

        $toDoToday = DB::table('trello_cards')
            ->select('trello_cards.*','trello_members.name as member_name','trello_lists.name as list_name')
            ->join('trello_members', 'trello_cards.member_id', '=', 'trello_members.id')
            ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
            ->where('member_id',$userId->id)
            ->where('is_archived',0)
            ->whereIn('trello_lists.id',  $trelloListOk)
            ->get();

        $problemsHavEncountered = DB::table('trello_cards')
            ->select('trello_cards.*','trello_members.name as member_name','trello_lists.name as list_name')
            ->join('trello_members', 'trello_cards.member_id', '=', 'trello_members.id')
            ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
            ->where('member_id',$userId->id)
            ->whereNotIn('trello_lists.id',  $trelloListNot)
            ->whereDate('last_activity','=', Carbon::yesterday())
            ->where('is_archived',0)
            ->get();

        $filtered = $problemsHavEncountered->filter(function ($value){
            return $value->total_time >= (($value->estimate * 20) + (($value->estimate * 20) *0.5));
        });

        $header = collect(['Name', 'TrelloList', 'TrelloMember','Estimate','Customer','Total Time','Is_Archived','Created_at', 'Updated_at']);

        return [
//            new Help,
        new CardTomorrowCount,
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
                        new \Mako\CustomTableCard\Table\Cell($order->name),
                        new \Mako\CustomTableCard\Table\Cell($order->list_name),
                        new \Mako\CustomTableCard\Table\Cell($order->member_name),
                        new \Mako\CustomTableCard\Table\Cell($order->estimate),
                        new \Mako\CustomTableCard\Table\Cell($order->customer),
                        new \Mako\CustomTableCard\Table\Cell($order->total_time),
                        new \Mako\CustomTableCard\Table\Cell($order->is_archived),
                        new \Mako\CustomTableCard\Table\Cell($order->created_at),
                        new \Mako\CustomTableCard\Table\Cell($order->updated_at),
                    ))->viewLink('/resources/trello-cards/'.$order->id);

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
                        new \Mako\CustomTableCard\Table\Cell($order->name),
                        new \Mako\CustomTableCard\Table\Cell($order->list_name),
                        new \Mako\CustomTableCard\Table\Cell($order->member_name),
                        new \Mako\CustomTableCard\Table\Cell($order->estimate),
                        new \Mako\CustomTableCard\Table\Cell($order->customer),
                        new \Mako\CustomTableCard\Table\Cell($order->total_time),
                        new \Mako\CustomTableCard\Table\Cell($order->is_archived),
                        new \Mako\CustomTableCard\Table\Cell($order->created_at),
                        new \Mako\CustomTableCard\Table\Cell($order->updated_at),
                    ))->viewLink('/resources/trello-cards/'.$order->id);

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
                ->data($filtered->map(function ($order)
                {
                    return (new \Mako\CustomTableCard\Table\Row(
                        new \Mako\CustomTableCard\Table\Cell($order->name),
                        new \Mako\CustomTableCard\Table\Cell($order->list_name),
                        new \Mako\CustomTableCard\Table\Cell($order->member_name),
                        new \Mako\CustomTableCard\Table\Cell($order->estimate),
                        new \Mako\CustomTableCard\Table\Cell($order->customer),
                        new \Mako\CustomTableCard\Table\Cell($order->total_time),
                        new \Mako\CustomTableCard\Table\Cell($order->is_archived),
                        new \Mako\CustomTableCard\Table\Cell($order->created_at),
                        new \Mako\CustomTableCard\Table\Cell($order->updated_at),
                    ))->viewLink('/resources/trello-cards/'.$order->id);

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
