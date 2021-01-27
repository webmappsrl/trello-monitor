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
//        $this->unirest = $unirest;
        parent::__construct();

    }


    public function handle()
    {
            $cards = resolve('TrelloCardsService');
            $cards = $cards->get_cards();
        foreach($cards as $index=>$card) {
                echo $index.' of '.count($cards)."\r\n";
                $card_di = resolve('TrelloCardService');
                $created_at = $card_di->get_first_date($card);
                $total_time = $card_di->get_total_time($card->id);
                $customer = $card_di->get_customer($card->id);
                $estimate = $card_di->get_estimate($card->id);
                $card_di->store_card($card,$total_time,$estimate,$customer,$created_at);
            }
        return 0;
    }


}
