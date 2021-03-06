<?php

namespace App\Http\Controllers;

use App\ClassU\Unirest;
use App\Models\TrelloBoard;
use App\Models\TrelloCard;
use App\Models\TrelloList;
use App\Models\TrelloMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrelloCardController extends Controller
{



    /**
     * Downlaod the cards from the specified board
     *
     * @param string $boardId_downloadCardsFromBoard
     * @return mixed
     */

//    public function _downloadCard(string $cardId, string $filter) {
//        $url = TRELLO_API_BASE_URL . $this->_getUrlCard($cardId, $filter);
//        $res = $this->unirest->get_data($url);
//        return $res;
//    }
//
//    public function _getUrlCard(string $cardId, string $filter)
//    {
//        $url = "/cards/{$cardId}/{$filter}";
//        return $url;
//
//    }
//
//    public function _getStorageCard(string $cardId, string $filter)
//    {
//        $url = "test_data/{$filter}{$cardId}.json";
//        return $url;
//    }
//
//    public function _downloadCard(string $cardId, string $filter) {
//        $url = TRELLO_API_BASE_URL . $this->_getUrlCard($cardId, $filter);
//        $res = $this->_unirest($url);
//        return $res;
//    }
//
//    public function _downloadBoard(string $cardId, string $filter) {
//        $match = ['actions', 'pluginData', 'customFieldItems'];
//        if (in_array($filter,$match))
//        {
//            $url = TRELLO_API_BASE_URL . $this->_getUrlCard($cardId, $filter);
//            $res = $this->_unirest($url);
//            return $res;
//        }
//        else
//        {
//            return "select the correct filter: {$filter}";
//        }
//
//    }
//
//    public function _downloadCardsFromBoard(string $boardId) {
//        $url = TRELLO_API_BASE_URL . "/boards/{$boardId}/cards";
//        $res = $this->_unirest($url);
//        return $res;
//    }
//
//    public function _downloadCardsArchive(string $boardId, string $filter) {
//        $url = TRELLO_API_BASE_URL . "/boards/{$boardId}/cards/{$filter}";
//        $res = $this->_unirest($url);
//        return $res;
//    }
//
//
//    /**
//     * Retrieve the data from the given trello url
//     *
//     * @param string $url the query url
//     * @return mixed
//     * @throws Exception
//     */
//    public function _unirest(string $url) {
//        if (empty(env('TRELLO_KEY')) || empty(env('TRELLO_TOKEN'))) {
//            throw new \Exception("Configuration missing: TRELLO_KEY and/or TRELLO_TOKEN are mandatory");
//        }
//        $headers = array('Accept' => 'application/json');
//        $query = array('key' => env('TRELLO_KEY'), 'token' => env('TRELLO_TOKEN'));
//        $r = \Unirest\Request::get($url, $headers, $query);
//        return $r->body;
//    }
//
//    public function _updateBoard($boardId) {
//        $board = TrelloBoard::query()->where("trello_id", "=", $boardId)->first();
//        if (is_null($board)) {
//            Log::debug("Updating board");
//            $url = TRELLO_API_BASE_URL . "/boards/{$boardId}";
//            $res = $this->_unirest($url);
//            if (!is_null($res)) {
//                $board = new TrelloBoard([
//                    'trello_id' => $res->id,
//                    'name' => $res->name
//                ]);
//                $board->save();
//            }
//        }
//
//        return $board;
//    }
//
//    public function _updateList($listId) {
//        $list = TrelloList::query()->where("trello_id", "=", $listId)->first();
//        if (is_null($list)) {
//            Log::debug("Updating list");
//            $url = TRELLO_API_BASE_URL . "/lists/{$listId}";
//            $res = $this->_unirest($url);
//
//            if (!is_null($res)) {
//                $list = new TrelloList([
//                    'trello_id' => $res->id,
//                    'name' => $res->name,
//                ]);
//
//                $list->save();
//            }
//        }
//
//        return $list;
//    }
//
//    public function _updateMember($memberId) {
//        $member = TrelloMember::query()->where("trello_id", "=", $memberId)->first();
//        if (is_null($member)) {
//            Log::debug("Updating member");
//            $url = TRELLO_API_BASE_URL . "/members/{$memberId}";
//            $res = $this->_unirest($url);
//            if (!is_null($res)) {
//                $member = new TrelloMember([
//                    'trello_id' => $res->id,
//                    'name' => $res->fullName,
//                ]);
//
//                $member->save();
//            }
//        }
//        return $member;
//    }




}
