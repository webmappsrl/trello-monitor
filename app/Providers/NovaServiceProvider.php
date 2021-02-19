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
        //find user and match trello_members
        $userRequestId = Auth::id();
        $user = User::find($userRequestId);

        //check day of the week
        $day_of_the_week = Carbon::now();
        $day_of_the_week = $day_of_the_week->dayOfWeek;

        if ( $day_of_the_week == 1) $date = Carbon::now()->subDays(3);
        else $date =  Carbon::yesterday();

        //filter list
        $trelloListNot = TrelloList::whereIn('name',['CYCLANDO OPTIMIZE','BACKLOG'])->pluck('id');
        $trelloListOk = TrelloList::whereIn('name',['TODAY','PROGRESS','REJECTED','ALMOST THERE'])->pluck('id');


        if ($user->role == 'admin')
        {

            $dvtpzzt  = TrelloMember::where('name','Davide Pizzato')->pluck('id');


            $didDoYesterdayDvtpzzt = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_members.name as member_name','trello_lists.name as list_name')
                ->join('trello_members', 'trello_cards.member_id', '=', 'trello_members.id')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$dvtpzzt)
                ->whereNotIn('list_id',  $trelloListNot)
                ->whereDate('last_progress_date','=', $date)
                ->get();

            $toDoTodayDvtpzzt = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_members.name as member_name','trello_lists.name as list_name')
                ->join('trello_members', 'trello_cards.member_id', '=', 'trello_members.id')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$dvtpzzt)
                ->where('is_archived',0)
                ->whereIn('list_id',  $trelloListOk)
                ->get();

            $filteredDvtpzzt = $didDoYesterdayDvtpzzt->filter(function ($value){
                return $value->total_time >= (($value->estimate * 20) + (($value->estimate * 20) *0.5));
            });

            $a=collect();

            foreach ($filteredDvtpzzt as $item)
            {
                $a->push($item);
            }

            $filteredDvtpzzt = $a;

            $pedramkat  = TrelloMember::where('name','pedramkat')->pluck('id');


            $didDoYesterdayPedramkat = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_members.name as member_name','trello_lists.name as list_name')
                ->join('trello_members', 'trello_cards.member_id', '=', 'trello_members.id')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$pedramkat)
                ->whereNotIn('list_id',  $trelloListNot)
                ->whereDate('last_progress_date','=', $date)
                ->get();

            $toDoTodayPedramkat = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_members.name as member_name','trello_lists.name as list_name')
                ->join('trello_members', 'trello_cards.member_id', '=', 'trello_members.id')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$pedramkat)
                ->where('is_archived',0)
                ->whereIn('list_id',  $trelloListOk)
                ->get();

            $filteredPedramkat = $didDoYesterdayPedramkat->filter(function ($value){
                return $value->total_time >= (($value->estimate * 20) + (($value->estimate * 20) *0.5));
            });

            $a=collect();

            foreach ($filteredPedramkat as $item)
            {
                $a->push($item);
            }

            $filteredPedramkat = $a;

            $gg  = TrelloMember::where('name','Gianmarco Gagliardi')->pluck('id');


            $didDoYesterdayGg = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_members.name as member_name','trello_lists.name as list_name')
                ->join('trello_members', 'trello_cards.member_id', '=', 'trello_members.id')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$gg)
                ->whereNotIn('list_id',  $trelloListNot)
                ->whereDate('last_progress_date','=', $date)
                ->get();

            $toDoTodayGg = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_members.name as member_name','trello_lists.name as list_name')
                ->join('trello_members', 'trello_cards.member_id', '=', 'trello_members.id')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$gg)
                ->where('is_archived',0)
                ->whereIn('list_id',  $trelloListOk)
                ->get();

            $filteredGg = $didDoYesterdayGg->filter(function ($value){
                return $value->total_time >= (($value->estimate * 20) + (($value->estimate * 20) *0.5));
            });

            $a=collect();

            foreach ($filteredGg as $item)
            {
                $a->push($item);
            }

            $filteredGg = $a;

            $mb  = TrelloMember::where('name','marcobarbieri70')->pluck('id');

            $didDoYesterdayMb =  DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_members.name as member_name','trello_lists.name as list_name')
                ->join('trello_members', 'trello_cards.member_id', '=', 'trello_members.id')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$mb)
                ->whereDate('last_progress_date','=', $date)
                ->get();

//            dd( $didDoYesterdayMb);

            $toDoTodayMb = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_members.name as member_name','trello_lists.name as list_name')
                ->join('trello_members', 'trello_cards.member_id', '=', 'trello_members.id')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$mb)
                ->where('is_archived',0)
                ->whereIn('list_id',  $trelloListOk)
                ->get();

            $filteredMb = $didDoYesterdayMb->filter(function ($value){
                return $value->total_time >= (($value->estimate * 20) + (($value->estimate * 20) *0.5));
            });

            $a=collect();

            foreach ($filteredMb as $item)
            {
                $a->push($item);
            }

            $filteredMb = $a;

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
                    ->title('Davide Pizzato'),
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
                    ->data($didDoYesterdayDvtpzzt->map(function ($order)
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
                    ->data($toDoTodayDvtpzzt->map(function ($order)
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
                    ->data($filteredDvtpzzt->map(function ($order)
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
                    ->title('Che problemi ho riscontrato?'),
                (new \Mako\CustomTableCard\CustomTableCard)
                    ->title('Pedram Katanchi'),
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
                    ->data($didDoYesterdayPedramkat->map(function ($order)
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
                    ->data($toDoTodayPedramkat->map(function ($order)
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
                    ->data($filteredPedramkat->map(function ($order)
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
                    ->title('Che problemi ho riscontrato?'),
                (new \Mako\CustomTableCard\CustomTableCard)
                    ->title('Gianmarco Gagliardi'),
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
                    ->data($didDoYesterdayGg->map(function ($order)
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
                    ->data($toDoTodayGg->map(function ($order)
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
                    ->data($filteredGg->map(function ($order)
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
                    ->title('Che problemi ho riscontrato?'),
                (new \Mako\CustomTableCard\CustomTableCard)
                    ->title('Marco Barbieri'),
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
                    ->data($didDoYesterdayMb->map(function ($order)
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
                    ->data($toDoTodayMb->map(function ($order)
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
                    ->data($filteredMb->map(function ($order)
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
        else
        {
            $userId = TrelloMember::where('name',$user->name)->first();

            $didDoYesterday = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_members.name as member_name','trello_lists.name as list_name')
                ->join('trello_members', 'trello_cards.member_id', '=', 'trello_members.id')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->join('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$userId->id)
                ->whereNotIn('list_id',  $trelloListNot)
                ->whereDate('last_progress_date','=', $date)
                ->get();

            $toDoToday = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_members.name as member_name','trello_lists.name as list_name')
                ->join('trello_members', 'trello_cards.member_id', '=', 'trello_members.id')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->join('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$userId->id)
                ->where('is_archived',0)
                ->whereIn('list_id',  $trelloListOk)
                ->get();

            $filtered = $didDoYesterday->filter(function ($value){
                return $value->total_time >= (($value->estimate * 20) + (($value->estimate * 20) *0.5));
            });

            $a=collect();

            foreach ($filtered as $item)
            {
                $a->push($item);
            }

            $filtered = $a;

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
            \ChrisWare\NovaBreadcrumbs\NovaBreadcrumbs::make(),

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
