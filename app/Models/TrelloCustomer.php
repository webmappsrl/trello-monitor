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



    public static function getNovaChartjsSettings(): array
    {
        $day = [];
        for ($i=1;$i<=30;$i++)
        {
            $day[]+=$i;
        }
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

        $names = $r->map(function($item, $key) {
            return  ['last_activity'=>Carbon::parse($item->last_activity)->dayOfYear];
        });

        $grouped = $names->groupBy('last_activity')->map(function ($row) {
            return $row->count();
        });
        $grouped = $grouped->all();
//        dd($grouped);
        $day = [];

        for ($i=1;$i<=30;$i++)
        {
            $day[$i]=0;
        }

        end($grouped);
        $last_key = key($grouped);
        foreach ($grouped as $index=>$item)
        {
            $i =  $last_key - $index;
            $day[$i+1] = ['data'=>$item];
        }

        $day = collect($day);

        return [

            'default' => [
                [
                    'label' => 'Cards by Day',
                    'borderColor' => '#f87900',
                    'fill' => '+1',
                    'backgroundColor' => 'rgba(20,20,20,0.2)',//For bar charts, this will be the fill color of the bar
                    'data' => $day->pluck('data'),
                ]
            ]
        ];
    }



}
