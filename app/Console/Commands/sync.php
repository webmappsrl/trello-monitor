<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Unirest\Request;

define("TRELLO_BASE_URL", "https://trello.con/b/");
define("TRELLO_API_BASE_URL", "https://api.trello.com/1");
define("TRELLO_BOARDS", [
    "SPRINT" => "qxqVS51D",
    "DEV" => "SRPXlaBI",
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
        Log::debug("Sync done");
        foreach (TRELLO_BOARDS as $beardName => $boardId) {
            $cards = $this->_downloadCardsFromBoard($boardId);

            dd($cards);
        }

        return 0;
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
