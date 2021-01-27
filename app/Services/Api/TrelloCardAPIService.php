<?php


namespace App\Services\Api;


use Unirest\Request;

class TrelloCardAPIService extends UnirestAPIService
{
    public function _getUrlCard(string $cardId, string $filter)
    {
        $url = "/cards/{$cardId}/{$filter}";
        return $url;
    }

    public function _downloadThirdPartCard(string $cardId, string $filter) {
        $url = env('TRELLO_API_BASE_URL') . $this->_getUrlCard($cardId, $filter);
        $res = $this->call($url);
        return $res;
    }

}
