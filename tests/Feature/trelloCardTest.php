<?php

namespace Tests\Feature;

use App\Services\TrelloCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;

use Tests\TestCase;

class trelloCardTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_mock_card()
    {
        $cards = json_decode(File::get("tests/test_data/cards.json"),FALSE);

        $mock = $this->mock(TrelloCardAPIService::class, function ($mock) use ($cards) {
            $mock->shouldReceive('_downloadCardsFromBoard')
                ->once()
                ->andReturn($cards);
            // gli faccio tornare il json che ho letto prima dal file
        });

        $mockedTrelloCardService = new TrelloCardService($mock);

        $iDatiCheTesto = $mockedTrelloCardService->_downloadCardsFromBoard();
        dd($iDatiCheTesto);




    }
}
