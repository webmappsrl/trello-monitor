<?php

namespace Tests\Feature;

use App\Services\Api\TrelloCardAPIService;
use App\Services\TrelloCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class estimateTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_check_estimate()
    {
        $card = json_decode(File::get("tests/Fixtures/card_89.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_89.json"),FALSE);

        $mock_estimate = $this->mock(TrelloCardAPIService::class, function ($mock) use ( $est, $card) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'pluginData')
                ->once()
                ->andReturn($est);
        });

        $mockedTrelloCardService = new TrelloCardService($mock_estimate);
        $estimate = $mockedTrelloCardService->get_estimate($card->id);
        $this->assertSame($estimate, 1);

        $card = json_decode(File::get("tests/Fixtures/card_99.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_99.json"),FALSE);

        $mock_estimate = $this->mock(TrelloCardAPIService::class, function ($mock) use ( $est, $card) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'pluginData')
                ->once()
                ->andReturn($est);
        });

        $mockedTrelloCardService = new TrelloCardService($mock_estimate);
        $estimate = $mockedTrelloCardService->get_estimate($card->id);
        $this->assertSame($estimate, 2);

        $card = json_decode(File::get("tests/Fixtures/card_59.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_59.json"),FALSE);

        $mock_estimate = $this->mock(TrelloCardAPIService::class, function ($mock) use ( $est, $card) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'pluginData')
                ->once()
                ->andReturn($est);
        });

        $mockedTrelloCardService = new TrelloCardService($mock_estimate);
        $estimate = $mockedTrelloCardService->get_estimate($card->id);
        $this->assertSame($estimate, 3);

        $card = json_decode(File::get("tests/Fixtures/card_70.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_70.json"),FALSE);

        $mock_estimate = $this->mock(TrelloCardAPIService::class, function ($mock) use ( $est, $card) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'pluginData')
                ->once()
                ->andReturn($est);
        });

        $mockedTrelloCardService = new TrelloCardService($mock_estimate);
        $estimate = $mockedTrelloCardService->get_estimate($card->id);
        $this->assertSame($estimate, 2);

        $card = json_decode(File::get("tests/Fixtures/card_94.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_94.json"),FALSE);

        $mock_estimate = $this->mock(TrelloCardAPIService::class, function ($mock) use ( $est, $card) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'pluginData')
                ->once()
                ->andReturn($est);
        });

        $mockedTrelloCardService = new TrelloCardService($mock_estimate);
        $estimate = $mockedTrelloCardService->get_estimate($card->id);
        $this->assertSame($estimate, 1);
    }
}
