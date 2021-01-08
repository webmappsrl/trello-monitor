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

    public function test_total_time_card_in_progress()
    {
        $tcc = new TrelloCardController();


        $actionCard = json_decode(Storage::get("test_total_time/total_time_1.json"),FALSE);
        $calcTime  = 0;
        $calcTime  = round($tcc->totalTime($calcTime,$actionCard));
        $calcTimeAction = 51.0;
        $this->assertSame($calcTime,$calcTimeAction);
        $calcTime  = 0;

        $actionCard = json_decode(Storage::get("test_total_time/total_time_2.json"),FALSE);
        $calcTime  = round($tcc->totalTime($calcTime,$actionCard));
        $calcTimeAction = 44.0;
        $this->assertSame($calcTime,$calcTimeAction);
        $calcTime  = 0;

        //card non messa in progress
        $actionCard = json_decode(Storage::get("test_total_time/total_time_3.json"),FALSE);
        $calcTime  = round($tcc->totalTime($calcTime,$actionCard));
        $calcTimeAction = 0.0;
        $this->assertSame($calcTime,$calcTimeAction);
        $calcTime  = 0;

        //card messa in to be tested direttamente
        $actionCard = json_decode(Storage::get("test_total_time/total_time_4.json"),FALSE);
        $calcTime  = round($tcc->totalTime($calcTime,$actionCard));
        $calcTimeAction = 0.0;
        $this->assertSame($calcTime,$calcTimeAction);
        $calcTime  = 0;

        $actionCard = json_decode(Storage::get("test_total_time/total_time_5.json"),FALSE);
        $calcTime  = round($tcc->totalTime($calcTime,$actionCard));
        $calcTimeAction = 7.0;
        $this->assertSame($calcTime,$calcTimeAction);
        $calcTime  = 0;

        $actionCard = json_decode(Storage::get("test_total_time/total_time_6.json"),FALSE);
        $calcTime  = round($tcc->totalTime($calcTime,$actionCard));
        $calcTimeAction = 18.0;
        $this->assertSame($calcTime,$calcTimeAction);
        $calcTime  = 0;

        $actionCard = json_decode(Storage::get("test_total_time/total_time_7.json"),FALSE);
        $calcTime  = round($tcc->totalTime($calcTime,$actionCard));
        $calcTimeAction = 88.0;
        $this->assertSame($calcTime,$calcTimeAction);
        $calcTime  = 0;

        $actionCard = json_decode(Storage::get("test_total_time/total_time_8.json"),FALSE);
        $calcTime  = round($tcc->totalTime($calcTime,$actionCard));
        $calcTimeAction = 11.0;
        $this->assertSame($calcTime,$calcTimeAction);
        $calcTime  = 0;

        $actionCard = json_decode(Storage::get("test_total_time/total_time_9.json"),FALSE);
        $calcTime  = round($tcc->totalTime($calcTime,$actionCard));
        $calcTimeAction = 239.0;
        $this->assertSame($calcTime,$calcTimeAction);
    }

}
