<?php

namespace App\Nova;

use App\Nova\Filters\Time;
use App\Nova\Filters\TrelloCustomer;
use App\Nova\Filters\TrelloIsArchived;
use App\Nova\Metrics\CardDoneCount;
use App\Nova\Metrics\CardProgressCount;
use App\Nova\Metrics\CardRejectedCount;
use App\Nova\Metrics\CardSumPointTodayDavidePizzato;
use App\Nova\Metrics\CardSumPointTodayGianmarcoGag;
use App\Nova\Metrics\CardSumPointTodayPedramKatanchi;
use App\Nova\Metrics\CardToBeRejectTodayDavidePizzato;
use App\Nova\Metrics\CardToBeRejectTodayGianmarcoGag;
use App\Nova\Metrics\CardToBeRejectTodayPedramKatanchi;
use App\Nova\Metrics\CardToBeRejectTodayPK;
use App\Nova\Metrics\CardToBeTestedCount;
use App\Nova\Metrics\CardToBeTestedTodayDavidePizzato;
use App\Nova\Metrics\CardToBeTestedTodayGianmarcoGag;
use App\Nova\Metrics\CardToBeTestedTodayPedramKatanchi;
use App\Nova\Metrics\CardToBeTestedTodayPK;
use App\Nova\Metrics\CardTodayCount;
use App\Nova\Metrics\CardTomorrowCount;
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
    public static $title = 'trello_id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'trello_id','name',
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
        return [
            new CardTomorrowCount,
            new CardTodayCount,
            new CardProgressCount,
            new CardToBeTestedCount,
            new CardRejectedCount,
            new CardDoneCount,
            new CardToBeTestedTodayPedramKatanchi,
            new CardToBeRejectTodayPedramKatanchi,
            new CardSumPointTodayPedramKatanchi,
            new CardToBeTestedTodayDavidePizzato,
            new CardToBeRejectTodayDavidePizzato,
            new CardSumPointTodayDavidePizzato,
            new CardToBeTestedTodayGianmarcoGag,
            new CardToBeRejectTodayGianmarcoGag,
            new CardSumPointTodayGianmarcoGag,
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
