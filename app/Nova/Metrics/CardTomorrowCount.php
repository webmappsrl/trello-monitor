<?php

namespace App\Nova\Metrics;

use App\Models\TrelloCard;
use App\Models\TrelloList;
use App\Nova\Sprint;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Nemrutco\NovaGlobalFilter\GlobalFilterable;

class CardTomorrowCount extends Value
{

    /**
     * Calculate the value of the metric.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $t = TrelloList::where('name', 'TOMORROW')->first();
        return $this->count($request, TrelloCard::where('is_archived', 0)->where('list_id', $t->id));
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
        return 'card-tomorrow-count';
    }
}
