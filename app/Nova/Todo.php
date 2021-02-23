<?php

namespace App\Nova;

use App\Nova\Filters\Time;
use App\Nova\Filters\TrelloCustomer;
use App\Nova\Filters\TrelloIsArchived;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Todo extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\TrelloCard::class;
    public static $displayInNavigation = false;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title ='trello_id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'trello_id','name'
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
            Text::make('Trello ID','trello_id', function () {
                return '<a href="trello-cards/'. $this->id . '" target="_blank">'. $this->trello_id . '</a>';
            })->asHtml()->sortable(),
            Text::make('Name')->sortable(),
//            ID::make(__('ID'), 'id')->sortable(),

            BelongsTo::make('TrelloList'),
            BelongsTo::make('TrelloMember'),
            BelongsTo::make('Customer'),
            Text::make('Estimate'),
            Number::make('Total Time'),
            Boolean::make('Archived','is_archived')
                ->trueValue('On')
                ->falseValue('Off'),

            Text::make('URL', function () {
                return '<a href="' . $this->link . '" target="_blank">URL Card</a>';
            })
                ->asHtml(),
            DateTime::make('created_at'),
            DateTime::make('updated_at'),

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
        return [

        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new \App\Nova\Filters\TrelloList(),
            new TrelloCustomer(),
            new \App\Nova\Filters\TrelloMember(),
            new Time(),
        ];
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
        return [

        ];
    }
}
