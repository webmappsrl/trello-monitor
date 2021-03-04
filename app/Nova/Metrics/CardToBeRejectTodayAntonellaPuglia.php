<?php

namespace App\Nova\Metrics;

use App\Models\TrelloCard;
use App\Models\TrelloList;
use App\Models\TrelloMember;
use Carbon\Carbon;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class CardToBeRejectTodayAntonellaPuglia extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $tbt = TrelloList::where('name', 'REJECTED')->first();
        $pk = TrelloMember::where('name', 'Antonella Puglia')->first();
        if (!empty($tbt))
            return $this->count($request, TrelloCard::where('is_archived', 0)->where('list_id', $tbt->id)->where('member_id', $pk->id)->whereDate('last_activity', '=', Carbon::today()));
        else return 0;
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            365 => __('Today'),
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'card-to-be-reject-today-antonella-puglia';
    }
}
