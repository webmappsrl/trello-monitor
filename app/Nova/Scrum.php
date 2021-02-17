<?php

namespace App\Nova;

use App\Models\TrelloList;
use App\Nova\Metrics\CardDoneCount;
use App\Nova\Metrics\CardProgressCount;
use App\Nova\Metrics\CardRejectedCount;
use App\Nova\Metrics\CardSumPointTodayDavidePizzato;
use App\Nova\Metrics\CardSumPointTodayGianmarcoGag;
use App\Nova\Metrics\CardSumPointTodayPedramKatanchi;
use App\Nova\Metrics\CardToBeRejectTodayDavidePizzato;
use App\Nova\Metrics\CardToBeRejectTodayGianmarcoGag;
use App\Nova\Metrics\CardToBeRejectTodayPedramKatanchi;
use App\Nova\Metrics\CardToBeTestedCount;
use App\Nova\Metrics\CardToBeTestedTodayDavidePizzato;
use App\Nova\Metrics\CardToBeTestedTodayGianmarcoGag;
use App\Nova\Metrics\CardToBeTestedTodayPedramKatanchi;
use App\Nova\Metrics\CardTodayCount;
use App\Nova\Metrics\CardTomorrowCount;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Scrum extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\TrelloMember::class;

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
            Text::make('Name'),
            Text::make('#Card', function () {
                return \App\Models\TrelloCard::where('member_id',$this->id)->where('is_archived',0)->count();
            }),
            Text::make('#Card Today', function () {
                $listToday= TrelloList::where('name','TODAY')->first();
                return \App\Models\TrelloCard::where('member_id',$this->id)->where('is_archived',0)->where('list_id',$listToday->id)->count();
            }),
            Text::make('∑ Card Points Today', function () {
                $listToday= TrelloList::where('name','TODAY')->first();
                    return \App\Models\TrelloCard::where('member_id',$this->id)->where('is_archived',0)->where('list_id',$listToday->id)->sum('estimate');
            }),
            Text::make('#Card Progress', function () {
                $listProgress= TrelloList::where('name','PROGRESS')->first();
                return \App\Models\TrelloCard::where('member_id',$this->id)->where('is_archived',0)->where('list_id',$listProgress->id)->count();
            }),
            Text::make('#Card Rej', function () {
                $listProgress= TrelloList::where('name','REJECTED')->first();
                return \App\Models\TrelloCard::where('member_id',$this->id)->where('is_archived',0)->where('list_id',$listProgress->id)->count();
            }),
            Text::make('∑ Rej Card Points Today ', function () {
                $listToday= TrelloList::where('name','REJECTED')->first();
                return \App\Models\TrelloCard::where('member_id',$this->id)->where('is_archived',0)->where('list_id',$listToday->id)->sum('estimate');
            }),
            Text::make('#Card TBT', function () {
                $listTBT= TrelloList::where('name','TO BE TESTED')->first();
                return \App\Models\TrelloCard::where('member_id',$this->id)->where('is_archived',0)->where('list_id',$listTBT->id)->count();
            }),
            Text::make('#Card Done', function () {
                $listDone= TrelloList::where('name','Done')->first();
                return \App\Models\TrelloCard::where('member_id',$this->id)->where('is_archived',0)->where('list_id',$listDone->id)->count();
            }),
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
