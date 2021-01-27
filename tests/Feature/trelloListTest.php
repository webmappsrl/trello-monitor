<?php

namespace Tests\Feature;

//use App\Nova\TrelloList;
use App\Models\TrelloCard;
use App\Models\TrelloList;
use App\Services\Api\TrelloListAPIService;
use App\Services\TrelloListService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class trelloListTest extends TestCase
{

    public function test_store_in_trello_lists_and_return_list()
    {

        $card = json_decode(File::get("tests/Fixtures/card_87.json"),FALSE);
        $list = json_decode(File::get("tests/Fixtures/list_87.json"),FALSE);

        $mock_list = $this->mock(TrelloListAPIService::class, function ($mock) use ($card, $list) {
            $mock->shouldReceive('_downloadListFromCard')
                ->with($card->idList)
                ->once()
                ->andReturn($list);
        });

        $mockedTrelloListService = new TrelloListService($mock_list);
        $list = $mockedTrelloListService->get_list($card->idList);

        $this->assertDatabaseHas('trello_lists',['name'=>$list->name,'trello_id'=>$list->trello_id]);
        TrelloList::destroy($list->id);

    }
}
