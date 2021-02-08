<?php


namespace App\Services;


use App\Models\TrelloCard;
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

}
