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
use Illuminate\Support\Facades\URL;
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
        $uri = URL::current();
        $resource = explode("/", $uri);
        $resource = $resource[count($resource)-1];
        if ($resource == 'customers')
        {
            Nova::style('customers',public_path('css/customers.css'));

        }
        else
        {
            Nova::style('field-text',public_path('css/fieldText.css'));
        }

        Nova::style('estimate-colors',public_path('css/estimateColors.css'));
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
                'pedramkatanchi@webmapp.it',
                'marcobarbieri@webmapp.it'
            ]);
        });
    }


    /**
     * Return the html to represent the estimate in the frontend
     *
     * @param number $total_time
     * @param number $estimate
     * @return string
     */
    private function getTimeP(float $total_time, float $estimate): string {
        $class = "estimate-low";
        if (round(($total_time/$estimate), 1, PHP_ROUND_HALF_DOWN) > 1 && round(($total_time/$estimate), 1, PHP_ROUND_HALF_DOWN) <= 1.25)
            $class = "estimate-medium";
        elseif (round(($total_time/$estimate), 1, PHP_ROUND_HALF_DOWN) > 1.25 && round(($total_time/$estimate), 1, PHP_ROUND_HALF_DOWN) <= 1.5)
            $class = "estimate-high";
        elseif (round(($total_time/$estimate), 1, PHP_ROUND_HALF_DOWN) > 1.5)
            $class = "estimate-superhigh";

        return '<p class="estimate-value ' . $class . '" style="font-weight: 700">' . $total_time.'/'.$estimate.'</p>';
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
                ->select('trello_cards.*','trello_customers.name as customer','trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$dvtpzzt)
                ->whereNotIn('list_id',  $trelloListNot)
                ->whereDate('last_progress_date','=', $date)
                ->get();

            $didDoYesterdayDvtpzzt = $didDoYesterdayDvtpzzt->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0 ) ? round(($value->total_time/20), 1, PHP_ROUND_HALF_DOWN): 0;
                return $value;
            });


            $toDoTodayDvtpzzt = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$dvtpzzt)
                ->where('is_archived',0)
                ->whereIn('list_id',  $trelloListOk)
                ->get();

            $toDoTodayDvtpzzt = $toDoTodayDvtpzzt->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0 ) ? round(($value->total_time/20), 1, PHP_ROUND_HALF_DOWN): 0;
                return $value;
            });


            $pedramkat  = TrelloMember::where('name','pedramkat')->pluck('id');


            $didDoYesterdayPedramkat = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$pedramkat)
                ->whereNotIn('list_id',  $trelloListNot)
                ->whereDate('last_progress_date','=', $date)
                ->get();

            $didDoYesterdayPedramkat =  $didDoYesterdayPedramkat->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0 ) ? round(($value->total_time/20), 1, PHP_ROUND_HALF_DOWN): 0;
                return $value;
            });

            $toDoTodayPedramkat = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$pedramkat)
                ->where('is_archived',0)
                ->whereIn('list_id',  $trelloListOk)
                ->get();


            $toDoTodayPedramkat =  $toDoTodayPedramkat->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0 ) ? round(($value->total_time/20), 1, PHP_ROUND_HALF_DOWN): 0;
                return $value;
            });

            $gg  = TrelloMember::where('name','Gianmarco Gagliardi')->pluck('id');


            $didDoYesterdayGg = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$gg)
                ->whereNotIn('list_id',  $trelloListNot)
                ->whereDate('last_progress_date','=', $date)
                ->get();

            $didDoYesterdayGg =   $didDoYesterdayGg->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0 ) ? round(($value->total_time/20), 1, PHP_ROUND_HALF_DOWN): 0;
                return $value;
            });

            $toDoTodayGg = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$gg)
                ->where('is_archived',0)
                ->whereIn('list_id',  $trelloListOk)
                ->get();

            $toDoTodayGg =   $toDoTodayGg->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0 ) ? round(($value->total_time/20), 1, PHP_ROUND_HALF_DOWN): 0;
                return $value;
            });

            $mb  = TrelloMember::where('name','marcobarbieri70')->pluck('id');

            $didDoYesterdayMb =  DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$mb)
                ->whereDate('last_progress_date','=', $date)
                ->get();

            $didDoYesterdayMb =   $didDoYesterdayMb->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0 ) ? round(($value->total_time/20), 1, PHP_ROUND_HALF_DOWN): 0;
                return $value;
            });

            $toDoTodayMb = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$mb)
                ->where('is_archived',0)
                ->whereIn('list_id',  $trelloListOk)
                ->get();

            $toDoTodayMb =   $toDoTodayMb->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0 ) ? round(($value->total_time/20), 1, PHP_ROUND_HALF_DOWN): 0;
                return $value;
            });

            $header = collect(['Name', 'TrelloList', 'TrelloMember','Estimate','Customer','Time','Is_Archived','Last Activity']);

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
                        new \Mako\CustomTableCard\Table\Cell('Customer'),
                        new \Mako\CustomTableCard\Table\Cell('Total Time'),
                        new \Mako\CustomTableCard\Table\Cell('Is_Archived'),
                        new \Mako\CustomTableCard\Table\Cell('Last Activity'),

                    ])
                    ->data($didDoYesterdayDvtpzzt->map(function ($order)
                    {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new \Mako\CustomTableCard\Table\Row(
                            new \Mako\CustomTableCard\Table\Cell($order->name),
                            new \Mako\CustomTableCard\Table\Cell($order->list_name),
                            new \Mako\CustomTableCard\Table\Cell($order->customer),
                            new \Mako\CustomTableCard\Table\Cell($time),
                            new \Mako\CustomTableCard\Table\Cell($order->is_archived),
                            new \Mako\CustomTableCard\Table\Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/'.$order->id);

                    })->toArray())
                    ->title('Che cosa ho fatto ieri?'),
                (new \Mako\CustomTableCard\CustomTableCard)
                    ->header([
                        new \Mako\CustomTableCard\Table\Cell('Name'),
                        new \Mako\CustomTableCard\Table\Cell('TrelloList'),
                        new \Mako\CustomTableCard\Table\Cell('Customer'),
                        new \Mako\CustomTableCard\Table\Cell('Time'),
                        new \Mako\CustomTableCard\Table\Cell('Is_Archived'),
                        new \Mako\CustomTableCard\Table\Cell('Last Activity'),

                    ])
                    ->data($toDoTodayDvtpzzt->map(function ($order)
                    {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new \Mako\CustomTableCard\Table\Row(
                            new \Mako\CustomTableCard\Table\Cell($order->name),
                            new \Mako\CustomTableCard\Table\Cell($order->list_name),
                            new \Mako\CustomTableCard\Table\Cell($order->customer),
                            new \Mako\CustomTableCard\Table\Cell($time),
                            new \Mako\CustomTableCard\Table\Cell($order->is_archived),
                            new \Mako\CustomTableCard\Table\Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/'.$order->id);

                    })->toArray())
                    ->title('Che cosa farò oggi?'),
                (new \Mako\CustomTableCard\CustomTableCard)
                    ->title('Pedram Katanchi'),
                (new \Mako\CustomTableCard\CustomTableCard)
                    ->header([
                        new \Mako\CustomTableCard\Table\Cell('Name'),
                        new \Mako\CustomTableCard\Table\Cell('TrelloList'),
                        new \Mako\CustomTableCard\Table\Cell('Customer'),
                        new \Mako\CustomTableCard\Table\Cell('Time'),
                        new \Mako\CustomTableCard\Table\Cell('Is_Archived'),
                        new \Mako\CustomTableCard\Table\Cell('Last Activity'),

                    ])
                    ->data($didDoYesterdayPedramkat->map(function ($order)
                    {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new \Mako\CustomTableCard\Table\Row(
                            new \Mako\CustomTableCard\Table\Cell($order->name),
                            new \Mako\CustomTableCard\Table\Cell($order->list_name),
                            new \Mako\CustomTableCard\Table\Cell($order->customer),
                            new \Mako\CustomTableCard\Table\Cell($time),
                            new \Mako\CustomTableCard\Table\Cell($order->is_archived),
                            new \Mako\CustomTableCard\Table\Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/'.$order->id);

                    })->toArray())
                    ->title('Che cosa ho fatto ieri?'),
                (new \Mako\CustomTableCard\CustomTableCard)
                    ->header([
                        new \Mako\CustomTableCard\Table\Cell('Name'),
                        new \Mako\CustomTableCard\Table\Cell('TrelloList'),
                        new \Mako\CustomTableCard\Table\Cell('Customer'),
                        new \Mako\CustomTableCard\Table\Cell('Time'),
                        new \Mako\CustomTableCard\Table\Cell('Is_Archived'),
                        new \Mako\CustomTableCard\Table\Cell('Last Activity'),

                    ])
                    ->data($toDoTodayPedramkat->map(function ($order)
                    {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new \Mako\CustomTableCard\Table\Row(
                            new \Mako\CustomTableCard\Table\Cell($order->name),
                            new \Mako\CustomTableCard\Table\Cell($order->list_name),
                            new \Mako\CustomTableCard\Table\Cell($order->customer),
                            new \Mako\CustomTableCard\Table\Cell($time),
                            new \Mako\CustomTableCard\Table\Cell($order->is_archived),
                            new \Mako\CustomTableCard\Table\Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/'.$order->id);

                    })->toArray())
                    ->title('Che cosa farò oggi?'),

                (new \Mako\CustomTableCard\CustomTableCard)
                    ->title('Gianmarco Gagliardi'),
                (new \Mako\CustomTableCard\CustomTableCard)
                    ->header([
                        new \Mako\CustomTableCard\Table\Cell('Name'),
                        new \Mako\CustomTableCard\Table\Cell('TrelloList'),
                        new \Mako\CustomTableCard\Table\Cell('Customer'),
                        new \Mako\CustomTableCard\Table\Cell('Time'),
                        new \Mako\CustomTableCard\Table\Cell('Is_Archived'),
                        new \Mako\CustomTableCard\Table\Cell('Last Activity'),

                    ])
                    ->data($didDoYesterdayGg->map(function ($order)
                    {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new \Mako\CustomTableCard\Table\Row(
                            new \Mako\CustomTableCard\Table\Cell($order->name),
                            new \Mako\CustomTableCard\Table\Cell($order->list_name),
                            new \Mako\CustomTableCard\Table\Cell($order->customer),
                            new \Mako\CustomTableCard\Table\Cell($time),
                            new \Mako\CustomTableCard\Table\Cell($order->is_archived),
                            new \Mako\CustomTableCard\Table\Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/'.$order->id);

                    })->toArray())
                    ->title('Che cosa ho fatto ieri?'),
                (new \Mako\CustomTableCard\CustomTableCard)
                    ->header([
                        new \Mako\CustomTableCard\Table\Cell('Name'),
                        new \Mako\CustomTableCard\Table\Cell('TrelloList'),
                        new \Mako\CustomTableCard\Table\Cell('Customer'),
                        new \Mako\CustomTableCard\Table\Cell('Time'),
                        new \Mako\CustomTableCard\Table\Cell('Is_Archived'),
                        new \Mako\CustomTableCard\Table\Cell('Last Activity'),

                    ])
                    ->data($toDoTodayGg->map(function ($order)
                    {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new \Mako\CustomTableCard\Table\Row(
                            new \Mako\CustomTableCard\Table\Cell($order->name),
                            new \Mako\CustomTableCard\Table\Cell($order->list_name),
                            new \Mako\CustomTableCard\Table\Cell($order->customer),
                            new \Mako\CustomTableCard\Table\Cell($time),
                            new \Mako\CustomTableCard\Table\Cell($order->is_archived),
                            new \Mako\CustomTableCard\Table\Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/'.$order->id);

                    })->toArray())
                    ->title('Che cosa farò oggi?'),
                (new \Mako\CustomTableCard\CustomTableCard)
                    ->title('Marco Barbieri'),
                (new \Mako\CustomTableCard\CustomTableCard)
                    ->header([
                        new \Mako\CustomTableCard\Table\Cell('Name'),
                        new \Mako\CustomTableCard\Table\Cell('TrelloList'),
                        new \Mako\CustomTableCard\Table\Cell('Customer'),
                        new \Mako\CustomTableCard\Table\Cell('Time'),
                        new \Mako\CustomTableCard\Table\Cell('Is_Archived'),
                        new \Mako\CustomTableCard\Table\Cell('Last Activity'),

                    ])
                    ->data($didDoYesterdayMb->map(function ($order)
                    {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new \Mako\CustomTableCard\Table\Row(
                            new \Mako\CustomTableCard\Table\Cell($order->name),
                            new \Mako\CustomTableCard\Table\Cell($order->list_name),
                            new \Mako\CustomTableCard\Table\Cell($order->customer),
                            new \Mako\CustomTableCard\Table\Cell($time),
                            new \Mako\CustomTableCard\Table\Cell($order->is_archived),
                            new \Mako\CustomTableCard\Table\Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/'.$order->id);

                    })->toArray())
                    ->title('Che cosa ho fatto ieri?'),
                (new \Mako\CustomTableCard\CustomTableCard)
                    ->header([
                        new \Mako\CustomTableCard\Table\Cell('Name'),
                        new \Mako\CustomTableCard\Table\Cell('TrelloList'),
                        new \Mako\CustomTableCard\Table\Cell('Customer'),
                        new \Mako\CustomTableCard\Table\Cell('Time'),
                        new \Mako\CustomTableCard\Table\Cell('Is_Archived'),
                        new \Mako\CustomTableCard\Table\Cell('Last Activity'),

                    ])
                    ->data($toDoTodayMb->map(function ($order)
                    {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new \Mako\CustomTableCard\Table\Row(
                            new \Mako\CustomTableCard\Table\Cell($order->name),
                            new \Mako\CustomTableCard\Table\Cell($order->list_name),
                            new \Mako\CustomTableCard\Table\Cell($order->customer),
                            new \Mako\CustomTableCard\Table\Cell($time),
                            new \Mako\CustomTableCard\Table\Cell($order->is_archived),
                            new \Mako\CustomTableCard\Table\Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/'.$order->id);

                    })->toArray())
                    ->title('Che cosa farò oggi?')
            ];
        }
        else
        {
            $userId = TrelloMember::where('name',$user->name)->first();

            $didDoYesterday = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->join('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$userId->id)
                ->whereNotIn('list_id',  $trelloListNot)
                ->whereDate('last_progress_date','=', $date)
                ->get();

            $didDoYesterday =   $didDoYesterday->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0 ) ? round(($value->total_time/20), 1, PHP_ROUND_HALF_DOWN): 0;
                return $value;
            });

            $toDoToday = DB::table('trello_cards')
                ->select('trello_cards.*','trello_customers.name as customer','trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->join('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id',$userId->id)
                ->where('is_archived',0)
                ->whereIn('list_id',  $trelloListOk)
                ->get();

            $toDoToday = $toDoToday->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0 ) ? round(($value->total_time/20), 1, PHP_ROUND_HALF_DOWN): 0;
                return $value;
            });


            $header = collect(['Name', 'TrelloList', 'TrelloMember','Estimate','Customer','Time','Is_Archived','Last Activity']);

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
                        new \Mako\CustomTableCard\Table\Cell('Customer'),
                        new \Mako\CustomTableCard\Table\Cell('Time'),
                        new \Mako\CustomTableCard\Table\Cell('Is_Archived'),
                        new \Mako\CustomTableCard\Table\Cell('Last Activity'),

                    ])
                    ->data($didDoYesterday->map(function ($order)
                    {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new \Mako\CustomTableCard\Table\Row(
                            new \Mako\CustomTableCard\Table\Cell($order->name),
                            new \Mako\CustomTableCard\Table\Cell($order->list_name),
                            new \Mako\CustomTableCard\Table\Cell($order->customer),
                            new \Mako\CustomTableCard\Table\Cell($time),
                            new \Mako\CustomTableCard\Table\Cell($order->is_archived),
                            new \Mako\CustomTableCard\Table\Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/'.$order->id);

                    })->toArray())
                    ->title('Che cosa ho fatto ieri?'),
                (new \Mako\CustomTableCard\CustomTableCard)
                    ->header([
                        new \Mako\CustomTableCard\Table\Cell('Name'),
                        new \Mako\CustomTableCard\Table\Cell('TrelloList'),
                        new \Mako\CustomTableCard\Table\Cell('Customer'),
                        new \Mako\CustomTableCard\Table\Cell('Time'),
                        new \Mako\CustomTableCard\Table\Cell('Is_Archived'),
                        new \Mako\CustomTableCard\Table\Cell('Last Activity'),

                    ])
                    ->data($toDoToday->map(function ($order)
                    {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new \Mako\CustomTableCard\Table\Row(
                            new \Mako\CustomTableCard\Table\Cell($order->name),
                            new \Mako\CustomTableCard\Table\Cell($order->list_name),
                            new \Mako\CustomTableCard\Table\Cell($order->customer),
                            new \Mako\CustomTableCard\Table\Cell($time),
                            new \Mako\CustomTableCard\Table\Cell($order->is_archived),
                            new \Mako\CustomTableCard\Table\Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/'.$order->id);

                    })->toArray())
                    ->title('Che cosa farò oggi?')
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

