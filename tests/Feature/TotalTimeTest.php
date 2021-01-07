<?php

namespace Tests\Feature;

use App\Http\Controllers\TrelloCardController;
use App\Models\TrelloCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        $tcc = new TrelloCardController();

        $cards = json_decode(Storage::get('test_data/cards.json'),TRUE);

        foreach($cards as $card)
        {
            $actionCard = json_decode(Storage::get("test_data/total_time_'{$card['id']}'.json"),TRUE);

            $calcTime=0;
            $notCalcTime= 0;
            $totalTimeAction= 0;
            for ($i = count($actionCard)-1; $i > 0; $i--)
            {
                $calcTime  = $tcc->totalTime($calcTime,$actionCard);
                $notCalcTime  = $tcc->notTotalTime($notCalcTime,$actionCard);
                $totalTimeAction = $tcc->allTotalTime($totalTimeAction,$actionCard);

            }
            $this->assertSame($notCalcTime+$calcTime,$totalTimeAction);

        }

    }

}
