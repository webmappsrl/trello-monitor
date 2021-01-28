<?php

namespace App\Console\Commands;

use App\ClassU\downloadCard;
use App\ClassU\Unirest;
use App\Http\Controllers\TrelloCardController;
use App\Services\TrelloCardsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Unirest\Request;
use App\Models\TrelloCard;
use App\Models\TrelloBoard;
use App\Models\TrelloList;
use App\Models\TrelloMember;
use function PHPUnit\Framework\isEmpty;

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
    protected $unirest;

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
            $cards = resolve('TrelloCardsService');
            $cards = $cards->get_cards();

            foreach($cards as $index=>$card) {

                echo $index.' of '.count($cards)."\r\n";
                $card_di = resolve('TrelloCardService');
                $member_di = resolve('TrelloMemberService');
                $list_di = resolve('TrelloListService');


//                $member = $member_di->get_member($card->idMembers);
//                if (!is_null($member)) $member = $member->id ?? $member ='';
//
//                $list = $list_di->get_list($card->idList);
//                if (!is_null($list)) $list = $list->id ?? $list ='';
$list = '';$member = '';
                //persist in trello_cards

                dd($card_di->set_archive($card,$cards));
                $card_di->store_card($card,$member,$list);
            }

        return 0;
    }


}
