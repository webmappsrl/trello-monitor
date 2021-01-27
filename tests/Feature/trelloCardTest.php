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

        $mockedTrelloCardService->store_card($card);

        $this->assertDatabaseHas('trello_cards',['name'=>$card->name,'link'=>$card->shortUrl,'total_time'=>4,'customer'=>'CAMPOS','estimate'=>1]);

    }

    public function test_update_card_yes()
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

        $dbCard = $mockedTrelloCardService->store_card($card);


        $card = json_decode(File::get("tests/Fixtures/card_87.json"),FALSE);
        $card->dateLastActivity = date('Y-m-d H:i:s',strtotime($dbCard->updated_at)+3600);
        $card->name = (string)rand(1,99999);

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

        $mockedTrelloCardService->store_card($card);

        $this->assertDatabaseHas('trello_cards',['name'=>$card->name,'link'=>$card->shortUrl,'total_time'=>4,'customer'=>'CAMPOS','estimate'=>1]);

    }

    public function test_update_card_no()
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

        $dbCard = $mockedTrelloCardService->store_card($card);


        $card = json_decode(File::get("tests/Fixtures/card_87.json"),FALSE);
        $card_old_name = $card->name;
        $card->name = (string)rand(1,99999);

        $tot_time = json_decode(File::get("tests/Fixtures/total_time_87.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_87.json"),FALSE);
        $customer = json_decode(File::get("tests/Fixtures/cf_87.json"),FALSE);

        $mock_total_time = $this->mock(TrelloCardAPIService::class, function ($mock) {
            $mock->shouldNotReceive('_downloadThirdPartCard');
        });

        //here I print the var mock if I do the DD
        $mockedTrelloCardService = new TrelloCardService($mock_total_time);

        $mockedTrelloCardService->store_card($card);

        $this->assertDatabaseHas('trello_cards',['name'=>$card_old_name,'link'=>$card->shortUrl,'total_time'=>4,'customer'=>'CAMPOS','estimate'=>1]);

    }

}
