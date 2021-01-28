<?php

namespace Tests\Feature;

use App\Models\TrelloCard;
use App\Models\TrelloList;
use App\Models\TrelloMember;
use App\Services\Api\TrelloCardAPIService;
use App\Services\Api\TrelloListAPIService;
use App\Services\Api\TrelloMemberAPIService;
use App\Services\TrelloCardService;
use App\Services\TrelloListService;
use App\Services\TrelloMemberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class setCardArchiveTest extends TestCase
{
    public function test_set_archive_yes()
    {
        Schema::disableForeignKeyConstraints();
        TrelloCard::truncate();
        TrelloMember::truncate();
        TrelloList::truncate();
        Schema::enableForeignKeyConstraints();

        $cards = json_decode(File::get("tests/Fixtures/cards.json"),FALSE);
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
                ->once()
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
        $card = $mockedTrelloCardService->store_card($card,$member->id,$list->id);

        $mockedTrelloCardService->set_archive($cards,$card);

        $this->assertSame($card->is_archived,1);
    }

    public function test_set_archive_no()
    {
        Schema::disableForeignKeyConstraints();
        TrelloCard::truncate();
        TrelloMember::truncate();
        TrelloList::truncate();
        Schema::enableForeignKeyConstraints();

        $cards = json_decode(File::get("tests/Fixtures/cards.json"),FALSE);
        $card = json_decode(File::get("tests/Fixtures/card_87.json"),FALSE);
        $card->id = (string) rand(1,999999999);

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
                ->once()
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
        $card = $mockedTrelloCardService->store_card($card,$member->id,$list->id);

        $mockedTrelloCardService->set_archive($cards,$card);

        $this->assertSame($card->is_archived,0);
    }

}
