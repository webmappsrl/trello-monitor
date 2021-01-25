<?php


namespace App\ClassU;
use Unirest\Request;

define("TRELLO_BASE_URL", "https://trello.con/b/");
define("TRELLO_API_BASE_URL", "https://api.trello.com/1");
define("TRELLO_BOARD_SPRINT", "qxqVS51D");

class TrelloApi
{

    public function set_url()
    {
        $url = TRELLO_API_BASE_URL . "/boards/".TRELLO_BOARD_SPRINT."/cards";
        return $url;
    }

    public function call(string $url) {
        if (empty(env('TRELLO_KEY')) || empty(env('TRELLO_TOKEN'))) {
            throw new \Exception("Configuration missing: TRELLO_KEY and/or TRELLO_TOKEN are mandatory");
        }
        $headers = array('Accept' => 'application/json');
        $query = array('key' => env('TRELLO_KEY'), 'token' => env('TRELLO_TOKEN'));
        $r = Request::get($url, $headers, $query);
        return $r->body;
    }
}
