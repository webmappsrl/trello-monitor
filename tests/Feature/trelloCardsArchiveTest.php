<?php

namespace Tests\Feature;

use App\Services\Api\TrelloCardsAPIService;
use App\Services\TrelloCardsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class trelloCardsArchiveTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_cards_by_archive()
    {
        $cards = json_decode(File::get("tests/Fixtures/cards.json"),FALSE);


        $mock = $this->mock(TrelloCardsAPIService::class, function ($mock) use ($cards) {
            $mock->shouldReceive('_downloadCardsFromArchive')
                ->once()
                ->andReturn($cards);
        });

        //here I print the var mock if I do the DD
        $mockedTrelloCardsService = new TrelloCardsService($mock);

        $data = $mockedTrelloCardsService->get_cards_archive();
        $this->assertSame(count($cards),count($data));
    }
}
