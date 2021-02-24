<?php

namespace App\Nova;

use App\Models\TrelloCard;
use App\Models\TrelloCustomer;
use App\Models\TrelloList;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use KirschbaumDevelopment\NovaChartjs\InlinePanel;


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
        'name',
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
            Text::make('Name','name', function () {
                return '<a href="customers/'. $this->id . '">'. $this->name . '</a>';
            })->asHtml()->sortable(),
            Number::make('Cards')->sortable(),
            Number::make('Todo')->sortable(),
            Number::make('Done')->sortable(),
            Number::make('All Days Worked','all_days_worked_customer')->sortable(),
            Number::make('Last 7 Days Worked','seven_days_worked_customer')->sortable(),
            Number::make('Last 30 Days Worked','thirty_days_worked_customer')->sortable(),



            DateTime::make('Last Activity Progress')->format('YYYY-MM-DD')->sortable(),
//            HasMany::make('TrelloCards'),

            HasMany::make('Todo'),
            HasMany::make('Done'),


            new InlinePanel($this, $request, 'Stats'),
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
