<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use App\ClassU\downloadCard;
use App\Models\TrelloCard;
use Unirest\Request;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use App\ClassU\Unirest;
use Unirest\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class trelloCardTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_update_customer()
    {
        // get single card data from file
        $cards = json_decode(File::get("tests/test_data/cards.json"),FALSE);
        $card = $cards[0];
        //create card
        $u = new downloadCard();
        $u->createCard($card);
        $dbCard = TrelloCard::query()->where("trello_id", "=", $card->id)->first();
        //mock of _downloadCard
        $this->mock(downloadCard::class, function ($mock) {
            $mock->shouldReceive('_downloadCard')
                ->once()
                ->andReturn(new Request(
                    File::get('tests/test_data/cf_1.json'),
                    $status =200,
                    $headers=[],
                ));
        });
        //operation
        $dbCard->setCustomer();
        //assert
        $this->assertEquals('CYCLANDO',$dbCard->customer);
    }

    public function test_update_estimate()
    {
        // get single card data from file
        $cards = json_decode(File::get("tests/test_data/cards.json"),FALSE);
        $card = $cards[0];
        $u = new downloadCard();
        //create card
        $u->createCard($card);
        $dbCard = TrelloCard::query()->where("trello_id", "=", $card->id)->first();
        //operation
        $res = $dbCard->setEstimate();
        $estimate = $dbCard->setEstimateValidate($res);
        //assert
        $this->assertEquals($estimate,$dbCard->estimate);
    }

    public function test_update_total_time()
    {

        // get single card data from file
        $cards = json_decode(File::get("tests/test_data/cards.json"),FALSE);
        $card = $cards[0];
        $u = new downloadCard();
        //create card
        $u->createCard($card);
        $dbCard = TrelloCard::query()->where("trello_id", "=", $card->id)->first();
        //operation
        $res = $dbCard->setTotalTime();
        $total_time = $dbCard->setTotalTimeValidate($res);
        //assert
        $this->assertEquals($total_time,$dbCard->total_time);
    }
}
