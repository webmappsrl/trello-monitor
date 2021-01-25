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

class unirestTest extends TestCase
{
    use RefreshDatabase;
    public function test_mock_get_data()
    {
        $this->mock(downloadCard::class, function ($mock) {
            $mock->shouldReceive('_downloadCardsFromBoard')
                ->once()
                ->andReturn(new Request(
                    File::get('tests/test_data/cards.json'),
                    $status =200,
                    $headers=[],
                ));
        });


        $response = $this->artisan('trello-monitor:sync');
    }

    public function test_mock_get_data_card()
    {
        // creteCard();
        // clean DB
        TrelloCard::truncate();

        // get single card data from file
        $cards = json_decode(File::get("tests/test_data/cards.json"),FALSE);
        $card = $cards[0];

        // createCard
        $u = new downloadCard();
        $u->createCard($card);

        // test downloadCard->createCard
        $dbCard = TrelloCard::query()->where("trello_id", "=", $card->id)->first();
        $this->assertNotNull($dbCard);
        $this->assertDatabaseHas('trello_cards',['name'=>$card->name,'link'=>$card->shortUrl]);

        // TEST NEED TO UPDATE
        $old_name = $card->name;
        $card->name = "NEW CARD NAME UPDATED ".rand(0,100000);
        $u->createCard($card);
        $this->assertDatabaseHas('trello_cards',['name'=>$old_name,'link'=>$card->shortUrl]);

        $card->dateLastActivity=date("Y-M-d H:i",strtotime("tomorrow"));
        $u->createCard($card);
        $this->assertDatabaseHas('trello_cards',['name'=>$card->name,'link'=>$card->shortUrl]);
    }
}
