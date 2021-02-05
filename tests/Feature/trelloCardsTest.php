<?php

namespace Tests\Feature;

use App\Models\TrelloCard;
use App\Models\TrelloList;
use App\Models\TrelloMember;
use App\Services\Api\TrelloCardAPIService;
use App\Services\Api\TrelloCardsAPIService;
use App\Services\Api\TrelloListAPIService;
use App\Services\Api\TrelloMemberAPIService;
use App\Services\TrelloCardService;
use App\Services\TrelloListService;
use App\Services\TrelloMemberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use App\Services\TrelloCardsService;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class trelloCardsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_mock_card()
    {
        $cards = json_decode(File::get("tests/Fixtures/cards.json"),FALSE);


        $mock = $this->mock(TrelloCardsAPIService::class, function ($mock) use ($cards) {
            $mock->shouldReceive('_downloadCardsFromBoard')
                ->once()
                ->andReturn($cards);
        });

        //here I print the var mock if I do the DD
        $mockedTrelloCardsService = new TrelloCardsService($mock);

        $data = $mockedTrelloCardsService->get_cards();
        $this->assertSame(count($cards),count($data));
    }

    public function test_delete_cards()
    {
        Schema::disableForeignKeyConstraints();
        TrelloCard::truncate();
        TrelloMember::truncate();
        TrelloList::truncate();
        Schema::enableForeignKeyConstraints();

        $cards = json_decode(File::get("tests/Fixtures/cards_delete_test.json"),FALSE);
        $cards_archive = json_decode(File::get("tests/Fixtures/cards_archive_delete_test.json"),FALSE);

        $mock = $this->mock(TrelloCardsAPIService::class, function ($mock) use ($cards_archive, $cards) {
            $mock->shouldReceive('_downloadCardsFromBoard')
                ->once()
                ->andReturn($cards);
            $mock->shouldReceive('_downloadCardsFromArchive')
                ->once()
                ->andReturn($cards_archive);
        });

        //here I print the var mock if I do the DD
        $mockedTrelloCardsService = new TrelloCardsService($mock);

        $data = $mockedTrelloCardsService->get_cards();
        $data_archived = $mockedTrelloCardsService->get_cards_archive();

        $card = json_decode(File::get("tests/Fixtures/card_147.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_147.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_147.json"),FALSE);
        $customer = json_decode(File::get("tests/Fixtures/cf_147.json"),FALSE);
        $list = json_decode(File::get("tests/Fixtures/list_147.json"),FALSE);
        $member = json_decode(File::get("tests/Fixtures/member_147.json"),FALSE);

        $mock_list = $this->mock(TrelloListAPIService::class, function ($mock) use ($card, $list) {
            $mock->shouldReceive('_downloadListFromCard')
                ->with($card->idList)
                ->once()
                ->andReturn($list);
        });

        $mock_member = $this->mock(TrelloMemberAPIService::class, function ($mock) use ($card, $member) {
            $mock->shouldReceive('_downloadMemberFromCard')
                ->with($card->idMembers[0])
                ->andReturn($member);
        });


        $mock= $this->mock(TrelloCardAPIService::class, function ($mock) use ($customer, $est, $card, $tot_time) {
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


        $mockedTrelloListService = new TrelloListService($mock_list);
        $list = $mockedTrelloListService->get_list($card->idList);

        //here I print the var mock if I do the DD
        $mockedTrelloMemberService = new TrelloMemberService($mock_member);
        $member = $mockedTrelloMemberService->get_member($card->idMembers);
        $mockedTrelloCardService = new TrelloCardService($mock);
        $mockedTrelloCardService->store_card($card,$member->id,$list->id);


//card 2

        $card = json_decode(File::get("tests/Fixtures/card_148.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_148.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_148.json"),FALSE);
        $customer = json_decode(File::get("tests/Fixtures/cf_148.json"),FALSE);
        $list = json_decode(File::get("tests/Fixtures/list_148.json"),FALSE);
        $member = json_decode(File::get("tests/Fixtures/member_128.json"),FALSE);

        $mock_list = $this->mock(TrelloListAPIService::class, function ($mock) use ($card, $list) {
            $mock->shouldReceive('_downloadListFromCard')
                ->with($card->idList)
                ->once()
                ->andReturn($list);
        });

        $mock_member = $this->mock(TrelloMemberAPIService::class, function ($mock) use ($card, $member) {
            $mock->shouldReceive('_downloadMemberFromCard')
                ->with($card->idMembers[0])
                ->andReturn($member);
        });


        $mock= $this->mock(TrelloCardAPIService::class, function ($mock) use ($customer, $est, $card, $tot_time) {
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


        $mockedTrelloListService = new TrelloListService($mock_list);
        $list = $mockedTrelloListService->get_list($card->idList);

        //here I print the var mock if I do the DD
        $mockedTrelloMemberService = new TrelloMemberService($mock_member);
        $member = $mockedTrelloMemberService->get_member($card->idMembers);

        $mockedTrelloCardService = new TrelloCardService($mock);
        $mockedTrelloCardService->store_card($card,$member->id,$list->id);


        //card 3

        $card = json_decode(File::get("tests/Fixtures/card_149.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_149.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_149.json"),FALSE);
        $customer = json_decode(File::get("tests/Fixtures/cf_149.json"),FALSE);
        $list = json_decode(File::get("tests/Fixtures/list_149.json"),FALSE);
        $member = json_decode(File::get("tests/Fixtures/member_73.json"),FALSE);

        $mock_list = $this->mock(TrelloListAPIService::class, function ($mock) use ($card, $list) {
            $mock->shouldReceive('_downloadListFromCard')
                ->with($card->idList)
                ->once()
                ->andReturn($list);
        });

        $mock_member = $this->mock(TrelloMemberAPIService::class, function ($mock) use ($card, $member) {
            $mock->shouldReceive('_downloadMemberFromCard')
                ->with($card->idMembers[0])
                ->andReturn($member);
        });


        $mock= $this->mock(TrelloCardAPIService::class, function ($mock) use ($customer, $est, $card, $tot_time) {
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


        $mockedTrelloListService = new TrelloListService($mock_list);
        $list = $mockedTrelloListService->get_list($card->idList);

        //here I print the var mock if I do the DD
        $mockedTrelloMemberService = new TrelloMemberService($mock_member);
        $member = $mockedTrelloMemberService->get_member($card->idMembers);
        $mockedTrelloCardService = new TrelloCardService($mock);
        $mockedTrelloCardService->store_card($card,$member->id,$list->id);

        //card 4

        $card = json_decode(File::get("tests/Fixtures/card_87.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_87.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_87.json"),FALSE);
        $customer = json_decode(File::get("tests/Fixtures/cf_87.json"),FALSE);
        $list = json_decode(File::get("tests/Fixtures/list_87.json"),FALSE);
        $member = json_decode(File::get("tests/Fixtures/member_87.json"),FALSE);

        $mock_list = $this->mock(TrelloListAPIService::class, function ($mock) use ($card, $list) {
            $mock->shouldReceive('_downloadListFromCard')
                ->with($card->idList)
                ->once()
                ->andReturn($list);
        });

        $mock_member = $this->mock(TrelloMemberAPIService::class, function ($mock) use ($card, $member) {
            $mock->shouldReceive('_downloadMemberFromCard')
                ->with($card->idMembers[0])
                ->andReturn($member);
        });


        $mock= $this->mock(TrelloCardAPIService::class, function ($mock) use ($customer, $est, $card, $tot_time) {
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

        $mockedTrelloListService = new TrelloListService($mock_list);
        $list = $mockedTrelloListService->get_list($card->idList);

        //here I print the var mock if I do the DD
        $mockedTrelloMemberService = new TrelloMemberService($mock_member);
        $member = $mockedTrelloMemberService->get_member($card->idMembers);
        $mockedTrelloCardService = new TrelloCardService($mock);
        $mockedTrelloCardService->store_card($card,$member->id,$list->id);

        $pre = TrelloCard::count();

        $mockedTrelloCardsService->delete_cards($data,$data_archived);

        $post = TrelloCard::count();

        $this->assertGreaterThan($post,$pre);
        $this->assertSame($post,3);
        $this->assertSame($pre,4);
        $deleteKey = TrelloCard::where('trello_id','6006810dd279f013d3decd8e')->count();
        $this->assertSame($deleteKey,0);





    }
}
