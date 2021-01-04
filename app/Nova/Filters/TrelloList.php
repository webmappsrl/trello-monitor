<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class TrelloList extends Filter
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
        return $query->where('list_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'Done' => '12',
            'Testing' => '11',
            'To be Tested' => 10,
            'Almost there' => 9,
            'Progress' => 8,
            'Today' => '7',
            'Tomorrow'=>'6',
            'After Tomorrow'=>'5',
            'After After Tomorrow'=>'4',
            'New'=>'3',
            'Backlog'=>'2',
            'Cyclando Optimize'=>'1'
        ];
    }
}
