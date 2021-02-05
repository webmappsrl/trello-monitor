<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Illuminate\Support\Facades\DB;

class TrelloCustomer extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('customer','like', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {

//        $customer = \App\Models\TrelloCard::select('customer')->distinct()->get()->toArray();
//        $data=[];
//        foreach ($data as $a => $b) {
//            $data += [$customer[$b] => $customer[$b]];
//        }

//        return [$data];
        return [
            'CYCLANDO'=> 'CYCLANDO',
            'VDC' => 'VDC',
            'WM-SERVER'=> 'WM-SERVER',
            'WM-APP' => 'WM-APP',
            'CBS'=>'CBS',
            'CAIPONTEDERA'=>'CAIPONTEDERA',
            'SISTECO'=>'SISTECO',
            'CAMPOS'=>'CAMPOS',
            'PN STELVIO'=>'PN STELVIO',
            'CAIPARMA'=>'CAIPARMA',
            'ITINERA ROMANICA PLUS'=>'ITINERA ROMANICA PLUS',
            'MPT'=>'MPT',
            'ER=U'=>'ER=U',
            'CAIRE'=>'CAIRE',
            'SICAI'=>'SICAI',
            'WM-WP'=>'WM-WP',
            'WM-MAPS'=>'WM-MAPS',
            'PEC'=>'PEC',
            'WM-TRELLO'=>'WM-TRELLO',
            'Pranzosanofuoricasa'=>'Pranzosanofuoricasa',
            'SGT'=>'SGT',
            'WM-HOQU'=>'WM-HOQU',
            'TIMESIS'=>'TIMESIS',
            'montepisanotree'=>'montepisanotree',
            'WM-WORDPRESS' =>'WM-WORDPRESS',
            'FIE'=>'FIE',
            'INTENSE'=>'INTENSE',
            'WM-BLOG' => 'WM-BLOG'
        ];
    }
}
