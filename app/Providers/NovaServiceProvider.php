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

class NovaServiceProvider extends NovaApplicationServiceProvider {
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
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
    protected function routes() {
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
    protected function gate() {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                'alessiopiccioli@webmapp.it',
                'andreadel84@gmail.com',
                'antonellapuglia@webmapp.it',
                'davidepizzato@webmapp.it',
                'pedramkatanchi@webmapp.it',
                'marcobarbieri@webmapp.it',
                'marco@eniacom.com'
            ]);
        });
    }

    /**
     * Return the html to represent the estimate in the frontend
     *
     * @param float $total_time
     * @param float $estimate
     *
     * @return string
     */
    private function getTimeP(float $total_time, float $estimate): string {
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
    protected function cards() {
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

        $result = [
            new CardTomorrowCount,
            new CardTodayCount,
            new CardProgressCount,
            new CardToBeTestedCount,
            new CardRejectedCount,
            new CardDoneCount
        ];

        if ($user->role == 'admin') {
            $users = [
                'Andrea Del Sarto' => 'Andrea Del Sarto',
                'Antonella Puglia' => 'Antonella Puglia',
                'Davide Pizzato' => 'Davide Pizzato',
                'marcobarbieri70' => 'Marco Barbieri',
                'Marco Fantoni' => 'Marco Fantoni',
                'pedramkat' => 'Pedram Katanchi'
            ];

            foreach ($users as $username => $name) {
                $lists = $this->getCustomListsPerUser(
                    $username,
                    $name,
                    $date,
                    $trelloListOk,
                    $trelloListNot
                );
                foreach ($lists as $list) {
                    $result[] = $list;
                }
            }
        } else {
            $lists = $this->getCustomListsPerUser(
                $user->name,
                $user->name,
                $date,
                $trelloListOk,
                $trelloListNot
            );
            foreach ($lists as $list) {
                $result[] = $list;
            }
        }

        return $result;
    }

    private function getCustomListsPerUser(string $username, string $name, $lastProgressDate, $inLists, $notInLists): array {
        $userId = TrelloMember::where('name', $username)->pluck('id')->toArray();

        if (isset($userId) && is_array($userId) && count($userId) > 0) {
            $userId = $userId[0];
            $yesterday = DB::table('trello_cards')
                ->select('trello_cards.*', 'trello_customers.name as customer', 'trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id', $userId);

            if (isset($notInLists) && is_array($notInLists))
                $yesterday = $yesterday->whereNotIn('list_id', $notInLists);

            $yesterday = $yesterday->whereDate('last_progress_date', '=', $lastProgressDate)
                ->get();

            $yesterday = $yesterday->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0) ? round(($value->total_time / 20), 1, PHP_ROUND_HALF_DOWN) : 0;

                return $value;
            });

            $today = DB::table('trello_cards')
                ->select('trello_cards.*', 'trello_customers.name as customer', 'trello_lists.name as list_name')
                ->join('trello_lists', 'trello_cards.list_id', '=', 'trello_lists.id')
                ->leftJoin('trello_customers', 'trello_cards.customer_id', '=', 'trello_customers.id')
                ->where('member_id', $userId)
                ->where('is_archived', 0);
            if (isset($inLists))
                $today = $today->whereIn('list_id', $inLists);

            $today = $today->get();

            $today = $today->map(function ($value, $key) {
                $value->total_time = ($value->total_time > 0) ? round(($value->total_time / 20), 1, PHP_ROUND_HALF_DOWN) : 0;

                return $value;
            });

            $result = [];

            $titleList = (new CustomTableCard())
                ->title($name);

            $result[] = $titleList;

            $yesterdayList = new CustomTableCard();
            $yesterdayList->title('Che cosa ho fatto ieri?');
            if (count($yesterday) > 0) {
                $yesterdayList->header([
                    new Cell('Name'),
                    new Cell('TrelloList'),
                    new Cell('Customer'),
                    new Cell('Total Time'),
                    new Cell('Is_Archived'),
                    new Cell('Last Activity'),
                ])
                    ->data($yesterday->map(function ($order) {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new Row(
                            new Cell($order->name),
                            new Cell($order->list_name),
                            new Cell($order->customer),
                            new Cell($time),
                            new Cell($order->is_archived),
                            new Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/' . $order->id);
                    })->toArray());
            } else
                $yesterdayList->data([new Row(new Cell($name . ' ieri non ha svolto attività su trello'))]);

            $result[] = $yesterdayList;

            $todayList = new CustomTableCard();
            $todayList->title('Che cosa farò oggi?');
            if (count($today) > 0) {
                $todayList->header([
                    new Cell('Name'),
                    new Cell('TrelloList'),
                    new Cell('Customer'),
                    new Cell('Time'),
                    new Cell('Is_Archived'),
                    new Cell('Last Activity'),
                ])
                    ->data($today->map(function ($order) {
                        $time = $this->getTimeP($order->total_time, $order->estimate);

                        return (new Row(
                            new Cell($order->name),
                            new Cell($order->list_name),
                            new Cell($order->customer),
                            new Cell($time),
                            new Cell($order->is_archived),
                            new Cell($order->last_progress_date)
                        ))->viewLink('/resources/trello-cards/' . $order->id);
                    })->toArray());
            } else $todayList->data([new Row(new Cell($name . ' non ha attività pianificate su trello per oggi'))]);
            $result[] = $todayList;

            return $result;
        } else return [];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards() {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools() {
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
    public function register() {
        //
    }
}
