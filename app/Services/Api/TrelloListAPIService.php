<?php


namespace App\Services\Api;


class TrelloListAPIService extends UnirestAPIService
{
    public function _downloadListFromCard($list_id) {
        echo "API downloadList!\n";
        $url = env('TRELLO_API_BASE_URL') . "/lists/{$list_id}";
        $res = $this->call($url);
        return $res;
    }



}
