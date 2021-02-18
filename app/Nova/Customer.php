<?php

namespace App\Nova;

use App\Models\TrelloCard;
use App\Models\TrelloList;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Customer extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\TrelloCustomer::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
//            ID::make(__('ID'), 'id')->sortable(),
            Text::make('Trello ID','trello_id', function () {
                return '<a href="customers/'. $this->id . '" target="_blank">'. $this->name . '</a>';
            })->asHtml()->sortable(),
            Text::make('Cards', function () {
                $card = TrelloCard::where('customer_id',$this->id)->where('is_archived',0)->count();
                $cardA = TrelloCard::where('customer_id',$this->id)->where('is_archived',1)->count();
                return  $card .' + '.$cardA;
            }),
            Text::make('Todo', function () {
                $today = TrelloList::where('name','DONE')->first();
                $card = TrelloCard::where('customer_id',$this->id)->where('is_archived',0)->where('list_id','!=' , $today->id)->count();
                return  $card;
            }),
            Text::make('Done', function () {
                $today = TrelloList::where('name','DONE')->first();
                $card = TrelloCard::where('customer_id',$this->id)->where('is_archived',0)->where('list_id' , $today->id)->count();
                $cardA = TrelloCard::where('customer_id',$this->id)->where('is_archived',1)->where('list_id' , $today->id)->count();
                return  $card .' + '.$cardA;
            }),
            Text::make('Last Activity', function () {
                $card = TrelloCard::where('customer_id',$this->id)->orderBy('last_progress_date', 'DESC')->first();
                return  $card->last_progress_date;
            }),
            HasMany::make('TrelloCards')

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
