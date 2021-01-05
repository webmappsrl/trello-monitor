<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Unirest\Request;
use App\Models\TrelloCard;
use App\Models\TrelloBoard;
use App\Models\TrelloList;
use App\Models\TrelloMember;
use function PHPUnit\Framework\isEmpty;

define("TRELLO_BASE_URL", "https://trello.con/b/");
define("TRELLO_API_BASE_URL", "https://api.trello.com/1");
define("TRELLO_BOARDS", [
    "SPRINT" => "qxqVS51D",
//    "DEV" => "SRPXlaBI",
//    "PROD",
//    "SISTECO"
]);

class sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trello-monitor:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all the cards from Trello boards to the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Starting sync");
        foreach (TRELLO_BOARDS as $beardName => $boardId) {
            $cards = $this->_downloadCardsFromBoard($boardId);
            foreach($cards as $card) {
                // find the card
                $dbCard = TrelloCard::query()->where("trello_id", "=", $card->id)->first();
                $board = $this->_updateBoard($card->idBoard);
                $list = $this->_updateList($card->idList);
                $member = null;
                if (is_array($card->idMembers) && count($card->idMembers) > 0)
                    $member = $this->_updateMember($card->idMembers[0]);

                $cf = $this->_downloadCardsCF($card->id);
                $estimate = $this->_downloadCardsEstimate($card->id);
                $total_time = $this->_downloadCardsAction($card->id);
                $m=0;
                for ($i = count($total_time)-1; $i > 0; $i--)
                {
                    if (isset($total_time[$i]->data->listAfter->name))
                    {
                        if ($total_time[$i]->data->listAfter->name == 'PROGRESS')
                        {
                            $minutes = abs(strtotime($total_time[$i]->date) - time()) / 60;
                            $minutes1 = abs(strtotime($total_time[$i-1]->date) - time()) / 60;
                            $m += $minutes - $minutes1;
                        }

                    }


                }

                if (is_null($dbCard)) {
                    // if not INSERT INTO creating the eventually missing board/list/member
                    Log::debug("INSERT INTO");
                    if (isset($cf[1]->value)){
                        $customer = $cf[1]->value->text;
                    }
                    else if (isset($cf[2]->value)){
                        $customer = $cf[2]->value->text;
                    }
                    else {
                        $customer = "";
                    }

                    if (isset($estimate[0]) && strlen($estimate[0]->value) > 0 && strpos($estimate[0]->value, 'estimate')) {
                        $estimate = explode("\"", $estimate[0]->value);
                        $newCard = new TrelloCard([
                            'trello_id' => $card->id,
                            'name' => $card->name,
                            'link' => $card->shortUrl,
                            'customer'=>$customer,
                            'estimate'=> $estimate[3],
                            'total_time'=>round($m)
//                        'date_last_activity' => date('Y-m-d h:i', strtotime($card->dateLastActivity)),
                        ]);
                    }
                    else
                    {
                        $newCard = new TrelloCard([
                            'trello_id' => $card->id,
                            'name' => $card->name,
                            'link' => $card->shortUrl,
                            'customer'=>$customer,
                            'estimate'=> 0,
                            'total_time'=>round($m)
//                        'date_last_activity' => date('Y-m-d h:i', strtotime($card->dateLastActivity)),
                        ]);
                    }


                    $newCard->board_id = $board->id;
                    $newCard->list_id = $list->id;
                    if (!is_null($member))
                        $newCard->member_id = $member->id;

                    $newCard->save();
                }
                else {
                    // if exists UPDATE !last modified/list/board/member
                    Log::debug("UPDATE");
//                    $this->_updateCard();
                }
            }
        }

        Log::info("Sync complete");

        return 0;
    }

    private function _updateBoard($boardId) {
        $board = TrelloBoard::query()->where("trello_id", "=", $boardId)->first();
        if (is_null($board)) {
            Log::debug("Updating board");
            $url = TRELLO_API_BASE_URL . "/boards/{$boardId}";
            $res = $this->_unirest($url);
            if (!is_null($res)) {
                $board = new TrelloBoard([
                    'trello_id' => $res->id,
                    'name' => $res->name
                ]);
                $board->save();
            }
        }

        return $board;
    }

    private function _updateList($listId) {
        $list = TrelloList::query()->where("trello_id", "=", $listId)->first();
        if (is_null($list)) {
            Log::debug("Updating list");
            $url = TRELLO_API_BASE_URL . "/lists/{$listId}";
            $res = $this->_unirest($url);

            if (!is_null($res)) {
                $list = new TrelloList([
                    'trello_id' => $res->id,
                    'name' => $res->name,
                ]);

                $list->save();
            }
        }

        return $list;
    }

    private function _updateMember($memberId) {
        $member = TrelloMember::query()->where("trello_id", "=", $memberId)->first();
        if (is_null($member)) {
            Log::debug("Updating member");
            $url = TRELLO_API_BASE_URL . "/members/{$memberId}";
            $res = $this->_unirest($url);
            if (!is_null($res)) {
                $member = new TrelloMember([
                    'trello_id' => $res->id,
                    'name' => $res->fullName,
                ]);

                $member->save();
            }
        }
        return $member;
    }

    /**
     * Downlaod the cards from the specified board
     *
     * @param string $boardId
     * @return mixed
     */
    private function _downloadCardsFromBoard(string $boardId) {
        $url = TRELLO_API_BASE_URL . "/boards/{$boardId}/cards";
        $res = $this->_unirest($url);

        return $res;
    }

    private function _downloadCardsCF(string $cardId) {
        $url = TRELLO_API_BASE_URL . "/cards/{$cardId}/customFieldItems";
        $res = $this->_unirest($url);

        return $res;
    }

    private function _downloadCardsAction(string $cardId) {
        $url = TRELLO_API_BASE_URL . "/cards/{$cardId}/actions";
        $res = $this->_unirest($url);

        return $res;
    }
    private function _downloadCardsEstimate(string $cardId) {
        $url = TRELLO_API_BASE_URL . "/cards/{$cardId}/pluginData";
        $res = $this->_unirest($url);

        return $res;
    }
    /**
     * Retrieve the data from the given trello url
     *
     * @param string $url the query url
     * @return mixed
     * @throws Exception
     */
    private function _unirest(string $url) {
        if (empty(env('TRELLO_KEY')) || empty(env('TRELLO_TOKEN'))) {
            throw new \Exception("Configuration missing: TRELLO_KEY and/or TRELLO_TOKEN are mandatory");
        }
        $headers = array('Accept' => 'application/json');
        $query = array('key' => env('TRELLO_KEY'), 'token' => env('TRELLO_TOKEN'));
        $r = Request::get($url, $headers, $query);
        return $r->body;
    }
}
