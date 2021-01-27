<?php


namespace App\Services\Api;


class TrelloMemberAPIService extends UnirestAPIService
{
    public function _downloadMemberFromCard($member_id) {
        echo "API downloadList!\n";
        $url = env('TRELLO_API_BASE_URL') . "/members/{$member_id}";
        $res = $this->call($url);
        return $res;
    }




}
