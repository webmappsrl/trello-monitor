<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use KirschbaumDevelopment\NovaChartjs\Traits\HasChart;
use KirschbaumDevelopment\NovaChartjs\Contracts\Chartable;
use phpDocumentor\Reflection\Types\Collection;


class TrelloCustomer extends Model implements Chartable
{
    use HasFactory;
    use HasChart;

    protected $fillable = ["id","trello_id", "name","last_activity_progress","cards","todo","done"];

    protected $casts = [
        'last_activity_progress' => 'datetime'
    ];

    public function todo() {
        $list = TrelloList::where('name','DONE')->first();
        return $this->hasMany(TrelloCard::class,'customer_id')
            ->where('is_archived',0)
            ->where('list_id','!=',$list->id);
    }


    public function done()
    {
        $list = TrelloList::where('name','DONE')->first();
        return $this->trelloCards()
            ->where(function ($query) use ($list) {
                $query->orWhere('is_archived', 1);
                $query->orWhereIn('list_id', TrelloList::select('id')->where('id', $list->id));
            });
    }

    public function trelloCards() {
        return $this->hasMany(TrelloCard::class,'customer_id');

    }

    public function array():array
    {
        $reportOut = TrelloCard::where('id',$this->id)->pluck('id')->toArray();

        return $reportOut;
    }

    public static function getNovaChartjsSettings(): array
    {
        $day = [];
        for ($i=0;$i<30;$i++)
        {
            $today_day_of_year = Carbon::now()->subDays($i);
            $day[]=$today_day_of_year->format('M-d-D');
        }
        $day = array_reverse($day);

        $month = [];
        for ($i=0;$i<12;$i++)
        {
            $today_day_of_year = Carbon::now()->subMonths($i);
            $month[]=$today_day_of_year->format('M Y');
        }
        $month = array_reverse($month);


        return [
            'default' => [
                'type' => 'bar',
                'titleProp' => 'name',
                'identProp' => 'id',
                'height' => 400,
                'indexColor' => '#999999',
                'color' => '#FF0000',
                'parameters' => $day,
                'options' => ['responsive' => true, 'maintainAspectRatio' => false],
            ],
            'second_chart' => [
                'type' => 'bar',
                'titleProp' => 'name',
                'identProp' => 'id',
                'height' => 400,
                'indexColor' => '#999999',
                'color' => '#FF0000',
                'parameters' => $month,
                'options' => ['responsive' => true, 'maintainAspectRatio' => false, 'offsetGridLines'=> false],
            ]
        ];
    }
    public function getAdditionalDatasets(): array
    {

        $list = TrelloList::where('name','DONE')->first();
        $r = TrelloCard::select('last_activity')
            ->orderBy('last_activity', 'asc')
            ->where('customer_id',$this->id)
            ->where('list_id',$list->id)

            ->whereDate('last_activity','>=', Carbon::now()->subMonth())
            ->get();

        $today_day_of_year = Carbon::now()->dayOfYear;
        $names = $r->map(function($item, $key) {
            return  ['last_activity'=>Carbon::parse($item->last_activity)->dayOfYear];
        });

        $grouped = $names->groupBy('last_activity')->map(function ($row) {
            return $row->count();
        });
        $grouped = $grouped->all();

        $day = [];

        for ($i=1;$i<=30;$i++)
        {
            $day[$i]=0;
        }

        foreach ($grouped as $index=>$item)
        {
            $i =  $today_day_of_year - $index;
            $day[$i+1] = ['data'=>$item];
        }
        $day = collect($day);
        $day = $day->pluck('data');
        $day = $day->all();
        $day = array_reverse($day);

        //month
        $listMonth = TrelloList::where('name','DONE')->first();
        $t = TrelloCard::select('last_activity')
            ->orderBy('last_activity', 'asc')
            ->where('customer_id',$this->id)
            ->where('list_id',$listMonth->id)
            ->whereDate('last_activity','>=', Carbon::now()->subYear())
            ->get();

        $today_month_of_year = Carbon::now();

        $month_names = $t->map(function($item) {
            return  ['last_activity'=>Carbon::parse($item->last_activity)->format('m Y')];
        });


        $month_grouped = $month_names->groupBy('last_activity')->map(function ($row) {
            return [$row->count()];
        });

//        dd($month_grouped);

        $month_grouped = $month_grouped->all();
//        dd($month_grouped);

        $month = [];

        for ($i=1;$i<=12;$i++)
        {
            $month[$i]=0;
        }

        foreach ($month_grouped as $index=>$item)
        {
//            var_dump($index);
            $date = explode(" ", $index);
//            var_dump($date);

            $date = $date[0].'/01/'.$date[1];
//            var_dump($date);
            $date = Carbon::parse($date);
//            var_dump(nl2br("\n"));

            $i = $date->diffInMonths($today_month_of_year);
//            var_dump(12-(12-$i));
            $month[(12-$i)] = ['data'=>$item];
        }

        $month = collect($month);

        return [

            'default' => [
                [
                    'label' => 'Cards by Day',
                    'borderColor' => '#f87900',
                    'fill' => '+1',
                    'backgroundColor' => 'rgba(100, 41, 64, 0.5)',//For bar charts, this will be the fill color of the bar
                    'data' => $day,
                ]
            ],
            'second_chart' => [
                [
                    'label' => 'Cards by Month',
                    'borderColor' => '#f87900',
                    'fill' => '+1',
                    'backgroundColor' => 'rgba(200, 54, 54, 0.5)',//For bar charts, this will be the fill color of the bar
                    'data' => $month->pluck('data'),
                ],
            ]

        ];
    }




}
