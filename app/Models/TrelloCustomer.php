<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use KirschbaumDevelopment\NovaChartjs\Traits\HasChart;
use KirschbaumDevelopment\NovaChartjs\Contracts\Chartable;



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

    public function done() {
        $list = TrelloList::where('name','DONE')->first();
        return $this->trelloCards()->where('is_archived',1)->orWhere(function($q) use($list){
            $q->where('is_archived',0)->where('list_id',$list->id);
        });
    }

    public function trelloCards() {
        return $this->hasMany(TrelloCard::class,'customer_id');

    }



    public static function getNovaChartjsSettings(): array
    {
        return [
            'default' => [
                'type' => 'line',
                'titleProp' => 'name',
                'identProp' => 'id',
                'height' => 400,
                'indexColor' => '#999999',
                'color' => '#FF0000',
                'parameters' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                'options' => ['responsive' => true, 'maintainAspectRatio' => false],
            ]
        ];
    }
    public function getAdditionalDatasets(): array
    {

        $r = TrelloCard::selectRaw('year(last_activity) year, monthname(last_activity) month, count(*) data')
            ->groupBy('year', 'month')
            ->orderBy('last_activity', 'asc')
            ->where('customer_id',$this->id)
            ->whereYear('last_activity','2021')
            ->get();

        return [

            'default' => [
                [
                    'label' => 'Cards by Month',
                    'borderColor' => '#f87900',
                    'fill' => '+1',
                    'backgroundColor' => 'rgba(20,20,20,0.2)',//For bar charts, this will be the fill color of the bar
                    'data' => $r->pluck('data'),
                ]
            ]
        ];
    }



}
