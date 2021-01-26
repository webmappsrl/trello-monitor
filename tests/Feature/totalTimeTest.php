<?php

namespace Tests\Feature;

use App\Services\Api\TrelloCardAPIService;
use App\Services\TrelloCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class totalTimeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_check_calculate_total_time()
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
        $calcTime = $mockedTrelloCardService->total_time($card->id);
        $calcTimeAction = 4.0;
        $this->assertSame($calcTime,$calcTimeAction);

        $card = json_decode(File::get("tests/Fixtures/card_17.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_17.json"),FALSE);

        $mock_total_time = $this->mock(TrelloCardAPIService::class, function ($mock) use ($card, $tot_time) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'actions')
                ->once()
                ->andReturn($tot_time);
        });
        $mockedTrelloCardService = new TrelloCardService($mock_total_time);
        $calcTime = $mockedTrelloCardService->total_time($card->id);
        $calcTimeAction = 0.0;
        $this->assertSame($calcTime,$calcTimeAction);

        $card = json_decode(File::get("tests/Fixtures/card_110.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_110.json"),FALSE);

        $mock_total_time = $this->mock(TrelloCardAPIService::class, function ($mock) use ($card, $tot_time) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'actions')
                ->once()
                ->andReturn($tot_time);
        });
        $mockedTrelloCardService = new TrelloCardService($mock_total_time);
        $calcTime = $mockedTrelloCardService->total_time($card->id);
        $calcTimeAction = 73.0;
        $this->assertSame($calcTime,$calcTimeAction);

        $card = json_decode(File::get("tests/Fixtures/card_76.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_76.json"),FALSE);

        $mock_total_time = $this->mock(TrelloCardAPIService::class, function ($mock) use ($card, $tot_time) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'actions')
                ->once()
                ->andReturn($tot_time);
        });
        $mockedTrelloCardService = new TrelloCardService($mock_total_time);
        $calcTime = $mockedTrelloCardService->total_time($card->id);
        $calcTimeAction = 155.0;
        $this->assertSame($calcTime,$calcTimeAction);

        $card = json_decode(File::get("tests/Fixtures/card_80.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_80.json"),FALSE);

        $mock_total_time = $this->mock(TrelloCardAPIService::class, function ($mock) use ($card, $tot_time) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'actions')
                ->once()
                ->andReturn($tot_time);
        });
        $mockedTrelloCardService = new TrelloCardService($mock_total_time);
        $calcTime = $mockedTrelloCardService->total_time($card->id);
        $calcTimeAction = 557.0;
        $this->assertSame($calcTime,$calcTimeAction);



    }
}
