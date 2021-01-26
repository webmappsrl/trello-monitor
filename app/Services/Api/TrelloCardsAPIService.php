<?php


namespace App\Services\Api;


class TrelloCardsAPIService extends UnirestAPIService
{
    public function _downloadCardsFromBoard() {
        echo "API downloadCards!\n";
        $url = TRELLO_API_BASE_URL . "/boards/".TRELLO_BOARDS_SPRINT."/cards";
        $res = $this->call($url);
        return $res;
    }
}
