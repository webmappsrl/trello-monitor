<?php

namespace App\Http\Controllers;

use App\Models\TrelloCard;
use Illuminate\Http\Request;

class TrelloCardController extends Controller
{
    public function _downloadCardsFromBoard(string $boardId) {
        $url = TRELLO_API_BASE_URL . "/boards/{$boardId}/cards";
        $res = $this->_unirest($url);

        return $res;
    }

    public function _downloadCardsCF(string $cardId) {
        $url = TRELLO_API_BASE_URL . "/cards/{$cardId}/customFieldItems";
        $res = $this->_unirest($url);

        return $res;
    }

    public function _downloadCardsAction(string $cardId) {
        $url = TRELLO_API_BASE_URL . "/cards/{$cardId}/actions";
        $res = $this->_unirest($url);

        return $res;
    }
    public function _downloadCardsEstimate(string $cardId) {
        $url = TRELLO_API_BASE_URL . "/cards/{$cardId}/pluginData";
        $res = $this->_unirest($url);

        return $res;
    }

    private function _unirest(string $url) {
        if (empty(env('TRELLO_KEY')) || empty(env('TRELLO_TOKEN'))) {
            throw new \Exception("Configuration missing: TRELLO_KEY and/or TRELLO_TOKEN are mandatory");
        }
        $headers = array('Accept' => 'application/json');
        $query = array('key' => env('TRELLO_KEY'), 'token' => env('TRELLO_TOKEN'));
        $r = \Unirest\Request::get($url, $headers, $query);
        return $r->body;
    }
}
