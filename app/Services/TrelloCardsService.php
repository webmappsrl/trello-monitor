<?php


namespace App\Services;


use App\Models\TrelloCard;
use App\Models\TrelloCustomer;
use App\Models\TrelloList;
use App\Services\Api\TrelloCardsAPIService;

class TrelloCardsService
{
    protected $trelloCardsApiService;

    public function __construct(TrelloCardsAPIService $trelloCardsApiService)
    {
        $this->trelloCardsApiService = $trelloCardsApiService;
    }

    public function get_cards()
    {
        return $this->trelloCardsApiService->_downloadCardsFromBoard();
    }

    public function get_cards_archive()
    {
        return $this->trelloCardsApiService->_downloadCardsFromArchive();
    }

    public function delete_cards($cards, $cards_archive)
    {
        $collectCards = collect($cards);
        $collectCards = $collectCards->pluck('id');

        $collectCardsArchive = collect($cards_archive);
        $collectCardsArchive = $collectCardsArchive->pluck('id');

        $allcardsMerged = $collectCards->merge($collectCardsArchive);
        $deleteKey = TrelloCard::whereNotIn('trello_id',$allcardsMerged)->pluck('id');
        TrelloCard::destroy($deleteKey);
    }

    public function add_customer_calculated_values()
    {
        $customers = TrelloCustomer::pluck('id');
        echo "\r\n";
        foreach ($customers as $index=>$customer)
        {
            echo "customer ".$index." of ".count($customers)."\r\n";
            $item = TrelloCustomer::find($customer);
            $done = TrelloList::where('name','DONE')->first();

            $cards = TrelloCard::where('customer_id',$customer)->count();
            $todo = TrelloCard::where('customer_id',$customer)->where('is_archived',0)->where('list_id','!=' , $done->id)->count();
            $done = TrelloCard::where('customer_id',$customer)->where('list_id', $done->id)->count();
            $last_activity_progress = TrelloCard::where('customer_id',$customer)->orderBy('last_progress_date', 'DESC')->first();

            if(strtotime($last_activity_progress->last_progress_date)!= strtotime("2000-02-17 09:04:53"))
            {
                $item->	last_activity_progress =  date('Y-m-d h:i:s', strtotime($last_activity_progress->last_progress_date));
            }
            $item->done = $done;
            $item->todo = $todo;
            $item->cards = $cards;
            $item->save();

        }
    }

}
