<?php


namespace App\Services;


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

}
