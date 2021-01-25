<?php


namespace App\Services;


use App\Services\Api\TrelloCardAPIService;

class TrelloCardService
{
    protected $trelloCardApiService;

    public function __construct(TrelloCardAPIService $trelloCardApiService)
    {
        $this->trelloCardApiService = $trelloCardApiService ?? new TrelloCardAPIService();
    }

}
