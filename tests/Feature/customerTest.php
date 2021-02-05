<?php

namespace Tests\Feature;

use App\Models\TrelloCard;
use App\Services\Api\TrelloCardAPIService;
use App\Services\TrelloCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class customerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_check_customer()
    {
        $card = json_decode(File::get("tests/Fixtures/card_17.json"),FALSE);
        $customer = json_decode(File::get("tests/Fixtures/cf_17.json"),FALSE);

        $mock_customer = $this->mock(TrelloCardAPIService::class, function ($mock) use ($customer, $card) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'customFieldItems')
                ->once()
                ->andReturn($customer);
        });

        //here I print the var mock if I do the DD
        $mockedTrelloCardService = new TrelloCardService($mock_customer);
        $customer = $mockedTrelloCardService->get_customer($card->id);
        $this->assertSame('UCVS',$customer);

        $card = json_decode(File::get("tests/Fixtures/card_47.json"),FALSE);
        $customer = json_decode(File::get("tests/Fixtures/cf_47.json"),FALSE);

        $mock_customer = $this->mock(TrelloCardAPIService::class, function ($mock) use ($customer, $card) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'customFieldItems')
                ->once()
                ->andReturn($customer);
        });

        //here I print the var mock if I do the DD
        $mockedTrelloCardService = new TrelloCardService($mock_customer);
        $customer = $mockedTrelloCardService->get_customer($card->id);
        $this->assertSame('WM-TRELLO',$customer);
    }
}
