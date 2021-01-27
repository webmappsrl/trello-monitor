<?php


namespace App\Services\Api;


class TrelloCardsAPIService extends UnirestAPIService
{
    public function _downloadCardsFromBoard() {
        echo "API downloadCards!\n";
        $url = env('TRELLO_API_BASE_URL') . "/boards/".env('TRELLO_BOARDS_SPRINT')."/cards";
        $res = $this->call($url);
        return $res;
    }
}
