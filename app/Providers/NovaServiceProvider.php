<?php

namespace App\Providers;

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
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Mako\CustomTableCard\CustomTableCard;
use Mako\CustomTableCard\Table\Cell;
use Mako\CustomTableCard\Table\Row;


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
        $resource = $resource[count($resource) - 1];
        if ($resource == 'customers') {
            Nova::style('customers', public_path('css/customers.css'));

        } else {
            Nova::style('field-text', public_path('css/fieldText.css'));
        }

        Nova::style('estimate-colors', public_path('css/estimateColors.css'));
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
                'antonellapuglia@webmapp.it',
                'davidepizzato@webmapp.it',
                'pedramkatanchi@webmapp.it',
                'marcobarbieri@webmapp.it'
            ]);
        });
    }


    /**
     * Return the html to represent the estimate in the frontend
     *
     * @param float $total_time
     * @param float $estimate
     * @return string
     */
    private function getTimeP(float $total_time, float $estimate): string
    {
        $class = "estimate-low";
        if ($total_time == 0 || $estimate == 0)
            $class = "estimate-low";
        else if (round(($total_time / $estimate), 1, PHP_ROUND_HALF_DOWN) > 1 && round(($total_time / $estimate), 1, PHP_ROUND_HALF_DOWN) <= 1.25)
            $class = "estimate-medium";
        elseif (round(($total_time / $estimate), 1, PHP_ROUND_HALF_DOWN) > 1.25 && round(($total_time / $estimate), 1, PHP_ROUND_HALF_DOWN) <= 1.5)
            $class = "estimate-high";
        elseif (round(($total_time / $estimate), 1, PHP_ROUND_HALF_DOWN) > 1.5)
            $class = "estimate-superhigh";

        return '<p class="estimate-value ' . $class . '" style="font-weight: 700">' . $total_time . '/' . $estimate . '</p>';
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
        $day_of_the_week = Carbon::now()->dayOfWeek;

        if ($day_of_the_week == 1) $date = Carbon::now()->subDays(3);
        else $date = Carbon::yesterday();

        //filter list
        $trelloListNot = TrelloList::whereIn('name', ['CYCLANDO OPTIMIZE', 'BACKLOG'])->pluck('id');
        $trelloListOk = TrelloList::whereIn('name', ['TODAY', 'PROGRESS', 'REJECTED', 'ALMOST THERE'])->pluck('id');

        if ($user->role == 'admin') {
            $dvdpzzt = TrelloMember::where('name', 'Davide Pizzato')->pluck('id');

            $didDoYesterdayDvdpzzt = DB::table('trello_cards')
                ->select('trello_cards.*', 'trello_customers.name as customer', 'trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id', $dvdpzzt)
                ->whereNotIn('list_id', $trelloListNot)
                ->whereDate('last_progress_date', '=', $date)
                ->get();

            $didDoYesterdayDvdpzzt = $didDoYesterdayDvdpzzt->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0) ? round(($value->total_time / 20), 1, PHP_ROUND_HALF_DOWN) : 0;
                return $value;
            });

            $toDoTodayDvdpzzt = DB::table('trello_cards')
                ->select('trello_cards.*', 'trello_customers.name as customer', 'trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id', $dvdpzzt)
                ->where('is_archived', 0)
                ->whereIn('list_id', $trelloListOk)
                ->get();

            $toDoTodayDvtdpzzt = $toDoTodayDvdpzzt->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0) ? round(($value->total_time / 20), 1, PHP_ROUND_HALF_DOWN) : 0;
                return $value;
            });

            $pedramkat = TrelloMember::where('name', 'pedramkat')->pluck('id');

            $didDoYesterdayPedramkat = DB::table('trello_cards')
                ->select('trello_cards.*', 'trello_customers.name as customer', 'trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id', $pedramkat)
                ->whereNotIn('list_id', $trelloListNot)
                ->whereDate('last_progress_date', '=', $date)
                ->get();

            $didDoYesterdayPedramkat = $didDoYesterdayPedramkat->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0) ? round(($value->total_time / 20), 1, PHP_ROUND_HALF_DOWN) : 0;
                return $value;
            });

            $toDoTodayPedramkat = DB::table('trello_cards')
                ->select('trello_cards.*', 'trello_customers.name as customer', 'trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id', $pedramkat)
                ->where('is_archived', 0)
                ->whereIn('list_id', $trelloListOk)
                ->get();

            $toDoTodayPedramkat = $toDoTodayPedramkat->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0) ? round(($value->total_time / 20), 1, PHP_ROUND_HALF_DOWN) : 0;
                return $value;
            });

            $antonella = TrelloMember::where('name', 'Antonella Puglia')->pluck('id');

            $didDoYesterdayAntonella = DB::table('trello_cards')
                ->select('trello_cards.*', 'trello_customers.name as customer', 'trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id', $antonella)
                ->whereNotIn('list_id', $trelloListNot)
                ->whereDate('last_progress_date', '=', $date)
                ->get();

            $didDoYesterdayAntonella = $didDoYesterdayAntonella->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0) ? round(($value->total_time / 20), 1, PHP_ROUND_HALF_DOWN) : 0;
                return $value;
            });

            $toDoTodayAntonella = DB::table('trello_cards')
                ->select('trello_cards.*', 'trello_customers.name as customer', 'trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id', $antonella)
                ->where('is_archived', 0)
                ->whereIn('list_id', $trelloListOk)
                ->get();

            $toDoTodayAntonella = $toDoTodayAntonella->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0) ? round(($value->total_time / 20), 1, PHP_ROUND_HALF_DOWN) : 0;
                return $value;
            });

            $mb = TrelloMember::where('name', 'marcobarbieri70')->pluck('id');

            $didDoYesterdayMb = DB::table('trello_cards')
                ->select('trello_cards.*', 'trello_customers.name as customer', 'trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id', $mb)
                ->whereDate('last_progress_date', '=', $date)
                ->get();

            $didDoYesterdayMb = $didDoYesterdayMb->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0) ? round(($value->total_time / 20), 1, PHP_ROUND_HALF_DOWN) : 0;
                return $value;
            });

            $toDoTodayMb = DB::table('trello_cards')
                ->select('trello_cards.*', 'trello_customers.name as customer', 'trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id', $mb)
                ->where('is_archived', 0)
                ->whereIn('list_id', $trelloListOk)
                ->get();

            $toDoTodayMb = $toDoTodayMb->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0) ? round(($value->total_time / 20), 1, PHP_ROUND_HALF_DOWN) : 0;
                return $value;
            });

            $header = collect(['Name', 'TrelloList', 'TrelloMember', 'Estimate', 'Customer', 'Time', 'Is_Archived', 'Last Activity']);

            return [
//            new Help,
                new CardTomorrowCount,
                new CardTodayCount,
                new CardProgressCount,
                new CardToBeTestedCount,
                new CardRejectedCount,
                new CardDoneCount,
                (new CustomTableCard)
                    ->title('Davide Pizzato'),
                (new CustomTableCard)
                    ->header([
                        new Cell('Name'),
                        new Cell('TrelloList'),
                        new Cell('Customer'),
                        new Cell('Total Time'),
                        new Cell('Is_Archived'),
                        new Cell('Last Activity'),

                    ])
                    ->data($didDoYesterdayDvdpzzt->map(function ($order) {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new Row(
                            new Cell($order->name),
                            new Cell($order->list_name),
                            new Cell($order->customer),
                            new Cell($time),
                            new Cell($order->is_archived),
                            new Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/' . $order->id);

                    })->toArray())
                    ->title('Che cosa ho fatto ieri?'),
                (new CustomTableCard)
                    ->header([
                        new Cell('Name'),
                        new Cell('TrelloList'),
                        new Cell('Customer'),
                        new Cell('Time'),
                        new Cell('Is_Archived'),
                        new Cell('Last Activity'),

                    ])
                    ->data($toDoTodayDvdpzzt->map(function ($order) {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new Row(
                            new Cell($order->name),
                            new Cell($order->list_name),
                            new Cell($order->customer),
                            new Cell($time),
                            new Cell($order->is_archived),
                            new Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/' . $order->id);

                    })->toArray())
                    ->title('Che cosa farò oggi?'),
                (new CustomTableCard)
                    ->title('Pedram Katanchi'),
                (new CustomTableCard)
                    ->header([
                        new Cell('Name'),
                        new Cell('TrelloList'),
                        new Cell('Customer'),
                        new Cell('Time'),
                        new Cell('Is_Archived'),
                        new Cell('Last Activity'),

                    ])
                    ->data($didDoYesterdayPedramkat->map(function ($order) {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new Row(
                            new Cell($order->name),
                            new Cell($order->list_name),
                            new Cell($order->customer),
                            new Cell($time),
                            new Cell($order->is_archived),
                            new Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/' . $order->id);

                    })->toArray())
                    ->title('Che cosa ho fatto ieri?'),
                (new CustomTableCard)
                    ->header([
                        new Cell('Name'),
                        new Cell('TrelloList'),
                        new Cell('Customer'),
                        new Cell('Time'),
                        new Cell('Is_Archived'),
                        new Cell('Last Activity'),

                    ])
                    ->data($toDoTodayPedramkat->map(function ($order) {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new Row(
                            new Cell($order->name),
                            new Cell($order->list_name),
                            new Cell($order->customer),
                            new Cell($time),
                            new Cell($order->is_archived),
                            new Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/' . $order->id);

                    })->toArray())
                    ->title('Che cosa farò oggi?'),

                (new CustomTableCard)
                    ->title('Antonella Puglia'),
                (new CustomTableCard)
                    ->header([
                        new Cell('Name'),
                        new Cell('TrelloList'),
                        new Cell('Customer'),
                        new Cell('Time'),
                        new Cell('Is_Archived'),
                        new Cell('Last Activity'),

                    ])
                    ->data($didDoYesterdayAntonella->map(function ($order) {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new Row(
                            new Cell($order->name),
                            new Cell($order->list_name),
                            new Cell($order->customer),
                            new Cell($time),
                            new Cell($order->is_archived),
                            new Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/' . $order->id);

                    })->toArray())
                    ->title('Che cosa ho fatto ieri?'),
                (new CustomTableCard)
                    ->header([
                        new Cell('Name'),
                        new Cell('TrelloList'),
                        new Cell('Customer'),
                        new Cell('Time'),
                        new Cell('Is_Archived'),
                        new Cell('Last Activity'),

                    ])
                    ->data($toDoTodayAntonella->map(function ($order) {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new Row(
                            new Cell($order->name),
                            new Cell($order->list_name),
                            new Cell($order->customer),
                            new Cell($time),
                            new Cell($order->is_archived),
                            new Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/' . $order->id);

                    })->toArray())
                    ->title('Che cosa farò oggi?'),
                (new CustomTableCard)
                    ->title('Marco Barbieri'),
                (new CustomTableCard)
                    ->header([
                        new Cell('Name'),
                        new Cell('TrelloList'),
                        new Cell('Customer'),
                        new Cell('Time'),
                        new Cell('Is_Archived'),
                        new Cell('Last Activity'),

                    ])
                    ->data($didDoYesterdayMb->map(function ($order) {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new Row(
                            new Cell($order->name),
                            new Cell($order->list_name),
                            new Cell($order->customer),
                            new Cell($time),
                            new Cell($order->is_archived),
                            new Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/' . $order->id);

                    })->toArray())
                    ->title('Che cosa ho fatto ieri?'),
                (new CustomTableCard)
                    ->header([
                        new Cell('Name'),
                        new Cell('TrelloList'),
                        new Cell('Customer'),
                        new Cell('Time'),
                        new Cell('Is_Archived'),
                        new Cell('Last Activity'),

                    ])
                    ->data($toDoTodayMb->map(function ($order) {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new Row(
                            new Cell($order->name),
                            new Cell($order->list_name),
                            new Cell($order->customer),
                            new Cell($time),
                            new Cell($order->is_archived),
                            new Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/' . $order->id);

                    })->toArray())
                    ->title('Che cosa farò oggi?')
            ];
        } else {
            $userId = TrelloMember::where('name', $user->name)->first();

            $didDoYesterday = DB::table('trello_cards')
                ->select('trello_cards.*', 'trello_customers.name as customer', 'trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->join('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id', $userId->id)
                ->whereNotIn('list_id', $trelloListNot)
                ->whereDate('last_progress_date', '=', $date)
                ->get();

            $didDoYesterday = $didDoYesterday->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0) ? round(($value->total_time / 20), 1, PHP_ROUND_HALF_DOWN) : 0;
                return $value;
            });

            $toDoToday = DB::table('trello_cards')
                ->select('trello_cards.*', 'trello_customers.name as customer', 'trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->join('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id', $userId->id)
                ->where('is_archived', 0)
                ->whereIn('list_id', $trelloListOk)
                ->get();

            $toDoToday = $toDoToday->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0) ? round(($value->total_time / 20), 1, PHP_ROUND_HALF_DOWN) : 0;
                return $value;
            });

            return [
                new CardTomorrowCount,
                new CardTodayCount,
                new CardProgressCount,
                new CardToBeTestedCount,
                new CardRejectedCount,
                new CardDoneCount,
                (new CustomTableCard)
                    ->header([
                        new Cell('Name'),
                        new Cell('TrelloList'),
                        new Cell('Customer'),
                        new Cell('Time'),
                        new Cell('Is_Archived'),
                        new Cell('Last Activity'),

                    ])
                    ->data($didDoYesterday->map(function ($order) {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new Row(
                            new Cell($order->name),
                            new Cell($order->list_name),
                            new Cell($order->customer),
                            new Cell($time),
                            new Cell($order->is_archived),
                            new Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/' . $order->id);

                    })->toArray())
                    ->title('Che cosa ho fatto ieri?'),
                (new CustomTableCard)
                    ->header([
                        new Cell('Name'),
                        new Cell('TrelloList'),
                        new Cell('Customer'),
                        new Cell('Time'),
                        new Cell('Is_Archived'),
                        new Cell('Last Activity'),

                    ])
                    ->data($toDoToday->map(function ($order) {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new Row(
                            new Cell($order->name),
                            new Cell($order->list_name),
                            new Cell($order->customer),
                            new Cell($time),
                            new Cell($order->is_archived),
                            new Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/' . $order->id);

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

