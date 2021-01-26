<?php

namespace Tests\Feature;

use App\Models\TrelloCard;
use App\Services\Api\TrelloCardAPIService;
use App\Services\TrelloCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;

use Tests\TestCase;

class trelloCardTest extends TestCase
{
    public function test_store_card()
    {
        TrelloCard::truncate();

        $card = json_decode(File::get("tests/Fixtures/card_87.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_87.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_87.json"),FALSE);
        $customer = json_decode(File::get("tests/Fixtures/cf_87.json"),FALSE);

        $mock_total_time = $this->mock(TrelloCardAPIService::class, function ($mock) use ($customer, $est, $card, $tot_time) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'actions')
                ->andReturn($tot_time);
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'pluginData')
                ->once()
                ->andReturn($est);
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'customFieldItems')
                ->once()
                ->andReturn($customer);
        });


        //here I print the var mock if I do the DD
        $mockedTrelloCardService = new TrelloCardService($mock_total_time);

        $itt = $mockedTrelloCardService->last_date($card);
        $total_time = $mockedTrelloCardService->total_time($card->id);

        $estimate = $mockedTrelloCardService->estimate($card->id);

        $customer = $mockedTrelloCardService->setCustomer($card->id);

        $mockedTrelloCardService->store_card($card,$total_time,$estimate,$customer,$itt);

        $this->assertDatabaseHas('trello_cards',['name'=>$card->name,'link'=>$card->shortUrl,'total_time'=>$total_time,'customer'=>$customer,'estimate'=>$estimate]);

    }

    public function test_update_card()
    {
        TrelloCard::truncate();

        $card = json_decode(File::get("tests/Fixtures/card_87.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_87.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_87.json"),FALSE);
        $customer = json_decode(File::get("tests/Fixtures/cf_87.json"),FALSE);

        $mock_total_time = $this->mock(TrelloCardAPIService::class, function ($mock) use ($customer, $est, $card, $tot_time) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'actions')
                ->andReturn($tot_time);
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'pluginData')
                ->once()
                ->andReturn($est);
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'customFieldItems')
                ->once()
                ->andReturn($customer);
        });


        //here I print the var mock if I do the DD
        $mockedTrelloCardService = new TrelloCardService($mock_total_time);
        $itt = $mockedTrelloCardService->last_date($card);
        $total_time = $mockedTrelloCardService->total_time($card->id);

        $estimate = $mockedTrelloCardService->estimate($card->id);

        $customer = $mockedTrelloCardService->setCustomer($card->id);

        $mockedTrelloCardService->store_card($card,$total_time,$estimate,$customer,$itt);


        $card = json_decode(File::get("tests/Fixtures/card_87_update.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_87.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_87.json"),FALSE);
        $customer = json_decode(File::get("tests/Fixtures/cf_87.json"),FALSE);

        $mock_total_time = $this->mock(TrelloCardAPIService::class, function ($mock) use ($customer, $est, $card, $tot_time) {
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'actions')
                ->andReturn($tot_time);
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'pluginData')
                ->once()
                ->andReturn($est);
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'customFieldItems')
                ->once()
                ->andReturn($customer);
        });

        //here I print the var mock if I do the DD
        $mockedTrelloCardService = new TrelloCardService($mock_total_time);
        $itt = $mockedTrelloCardService->last_date($card);
        $total_time = $mockedTrelloCardService->total_time($card->id);

        $estimate = $mockedTrelloCardService->estimate($card->id);

        $customer = $mockedTrelloCardService->setCustomer($card->id);

        $mockedTrelloCardService->store_card($card,$total_time,$estimate,$customer,$itt);

        $this->assertDatabaseHas('trello_cards',['name'=>$card->name,'link'=>$card->shortUrl,'total_time'=>$total_time,'customer'=>$customer,'estimate'=>$estimate]);

    }

}
