<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use App\Models\TrelloCard;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class ExportCard extends DownloadExcel implements WithMapping, WithHeadings
{
    use InteractsWithQueue, Queueable;

//    /**
//     * Perform the action on the given models.
//     *
//     * @param  \Laravel\Nova\Fields\ActionFields  $fields
//     * @param  \Illuminate\Support\Collection  $models
//     * @return mixed
//     */
//    public function handle(ActionFields $fields, Collection $models)
//    {
//        //
//    }
//
//    /**
//     * Get the fields available on the action.
//     *
//     * @return array
//     */
//    public function fields()
//    {
//        return [];
//    }

    public function headings(): array
    {
        return [
            'Name',
            'Url',
            'Status',
            'List',
            'Member',
            'Estimate',
            'Total Time',
            'Last Activity',

        ];
    }

    public function map($card):array
    {

        $status = ($card->is_archivied == 0 && $card->trelloList->name != 'DONE' ) ? 'TODO' : 'DONE';
        $trello_list_name = (isset( $card->trelloList->name) ) ?  $card->trelloList->name : '';
        $trello_list_member = (isset( $card->trelloMember->name) ) ?  $card->trelloMember->name : '';
        
        return [
            $card->name,
            $card->link,
            $status,
            $trello_list_name,
            $trello_list_member,
            $card->estimate,
            $card->total_time,
            $card->last_activity,
        ];


    }
}
