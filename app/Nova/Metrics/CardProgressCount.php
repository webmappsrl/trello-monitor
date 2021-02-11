<?php

namespace App\Nova\Metrics;

use App\Models\TrelloCard;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class CardProgressCount extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, TrelloCard::where('is_archived',0)->where('list_id',7));
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            30 => __('30 Days'),
            'TODAY' => __('Today'),
            2 => __('Yesterday'),//bad solutions
            7 => __('Week'),
            60 => __('60 Days'),
            90 => __('90 Days'),
            365 => __('365 Days'),
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
        return 'card-progress-count';
    }
}
