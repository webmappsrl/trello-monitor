<?php

namespace Tests\Feature;

use App\Models\TrelloCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use App\Console\Commands\sync;
use Unirest\Request;

class TotalTimeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function testTot()
    {
        foreach (TRELLO_BOARDS as $beardName => $boardId) {
            $cards = $this->_downloadCardsFromBoard($boardId);
            foreach($cards as $card)
            {
                $actionCard = $this->_downloadCardsAction($card->id);
                $calcTime=0;
                $notCalcTime= 0;
                $totalTimeAction= 0;
                for ($i = count($actionCard)-1; $i > 0; $i--)
                {
                    if (isset($actionCard[$i]->data->listAfter->name))
                    {
                        if ($actionCard[$i]->data->listAfter->name == 'PROGRESS')
                        {
                            $minutes = abs(strtotime($actionCard[$i]->date) - time()) / 60;
                            $minutes1 = abs(strtotime($actionCard[$i-1]->date) - time()) / 60;
                            $calcTime += $minutes - $minutes1;
                        }
                        if ($actionCard[$i]->data->listAfter->name != 'PROGRESS')
                        {
                            $minutes2 = abs(strtotime($actionCard[$i]->date) - time()) / 60;
                            $minutes3 = abs(strtotime($actionCard[$i-1]->date) - time()) / 60;
                            $notCalcTime += $minutes2 - $minutes3;
                        }

                        $min = abs(strtotime($actionCard[$i]->date) - time()) / 60;
                        $min1 = abs(strtotime($actionCard[$i-1]->date) - time()) / 60;
                        $totalTimeAction += $min - $min1;
                    }

                }
                $this->assertSame($notCalcTime+$calcTime,$totalTimeAction);

            }


        }

    }

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
