<?php

namespace Tests\Feature;

use App\Services\Api\TrelloCardAPIService;
use App\Services\TrelloCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class firstDateTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_check_first_date()
    {
        $card = json_decode(File::get("tests/Fixtures/card_87.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_87.json"),FALSE);

        $mock_total_time = $this->mock(TrelloCardAPIService::class, function ($mock) use ($card, $tot_time) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'actions')
                ->once()
                ->andReturn($tot_time);
        });
        $mockedTrelloCardService = new TrelloCardService($mock_total_time);
        $created_at = $mockedTrelloCardService->get_first_date($card);
        $this->assertEquals($created_at,$tot_time[count($tot_time)-1]->date);

        $card = json_decode(File::get("tests/Fixtures/card_12.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_12.json"),FALSE);

        $mock_total_time = $this->mock(TrelloCardAPIService::class, function ($mock) use ($card, $tot_time) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'actions')
                ->once()
                ->andReturn($tot_time);
        });
        $mockedTrelloCardService = new TrelloCardService($mock_total_time);
        $created_at = $mockedTrelloCardService->get_first_date($card);
        $this->assertEquals($created_at,$card->dateLastActivity);
    }
}
