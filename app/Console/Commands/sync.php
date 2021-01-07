<?php

namespace App\Console\Commands;

use App\Http\Controllers\TrelloCardController;
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
        $tcc = new TrelloCardController();


        Log::info("Starting sync");
        foreach (TRELLO_BOARDS as $beardName => $boardId) {
            $cards = $tcc->_downloadCardsFromBoard($boardId);

            foreach($cards as $card) {
                // find the card
                $dbCard = TrelloCard::query()->where("trello_id", "=", $card->id)->first();
                $board = $tcc->_updateBoard($card->idBoard);
                $list = $tcc->_updateList($card->idList);
                $member = null;
                if (is_array($card->idMembers) && count($card->idMembers) > 0)
                    $member = $tcc->_updateMember($card->idMembers[0]);

                $cf = $tcc->_downloadCard($card->id, 'customFieldItems');
                $estimate = $tcc->_downloadCard($card->id, 'pluginData');
                $total_time = $tcc->_downloadCard($card->id, 'actions');
                $m=0;
                $m = $tcc->totalTime($m,$total_time);

                if (is_null($dbCard)) {
                    // if not INSERT INTO creating the eventually missing board/list/member
                    Log::debug("INSERT INTO");
                    if (is_array($cf))
                    {
                        if (isset($cf[1]->value)){
                            $customer = $cf[1]->value->text;
                        }
                        else if (isset($cf[2]->value)){
                            $customer = $cf[2]->value->text;
                        }
                    }
                    else {
                        $customer = "";
                    }
                    if (is_array($estimate))
                    {
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
                            ]);
                        }
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


}
