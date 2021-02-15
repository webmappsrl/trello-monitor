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

class Sprint extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\TrelloCard::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

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
            ID::make(__('ID'), 'id')->sortable(),
            Text::make('Name'),
            BelongsTo::make('TrelloList'),
            BelongsTo::make('TrelloMember'),
            Text::make('Estimate'),
            Text::make('Customer'),
            Number::make('Total Time'),
            Boolean::make('Is_Archived')
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
        return [
            new \App\Nova\Filters\TrelloList(),
            new TrelloCustomer(),
            new TrelloIsArchived(),
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
        return [];
    }
}
