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

class trelloCardTest extends TestCase
{
    public function test_store_card()
    {

        Schema::disableForeignKeyConstraints();
        TrelloCard::truncate();
        TrelloMember::truncate();
        TrelloList::truncate();
        Schema::enableForeignKeyConstraints();


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

        $this->assertDatabaseHas('trello_cards',['name'=>$card->name,'link'=>$card->shortUrl,'total_time'=>4,'customer'=>'CAMPOS','estimate'=>1]);

    }

    public function test_update_card_yes()
    {
        Schema::disableForeignKeyConstraints();
        TrelloCard::truncate();
        TrelloMember::truncate();
        TrelloList::truncate();
        Schema::enableForeignKeyConstraints();

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
        $dbCard = $mockedTrelloCardService->store_card($card,$member->id,$list->id);


        $card = json_decode(File::get("tests/Fixtures/card_87.json"),FALSE);
        $card->dateLastActivity = date('Y-m-d H:i:s',strtotime($dbCard->updated_at)+3600);
        $card->name = (string)rand(1,99999);

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

//        $this->assertDatabaseHas('trello_cards',['name'=>$card->name,'link'=>$card->shortUrl,'total_time'=>4,'customer'=>'CAMPOS','estimate'=>1]);

    }

    public function test_update_card_no()
    {
        Schema::disableForeignKeyConstraints();
        TrelloCard::truncate();
        TrelloMember::truncate();
        TrelloList::truncate();
        Schema::enableForeignKeyConstraints();

        $card = json_decode(File::get("tests/Fixtures/card_87.json"),FALSE);
        $tot_time = json_decode(File::get("tests/Fixtures/total_time_87.json"),FALSE);
        $est = json_decode(File::get("tests/Fixtures/estimate_87.json"),FALSE);
        $customer = json_decode(File::get("tests/Fixtures/cf_87.json"),FALSE);
        $list = json_decode(File::get("tests/Fixtures/list_87.json"),FALSE);
        $member = json_decode(File::get("tests/Fixtures/member_87.json"),FALSE);

        $mock_list = $this->mock(TrelloListAPIService::class, function ($mock) use ($card, $list) {
            $mock->shouldReceive('_downloadListFromCard')
                ->with($card->idList)
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
                ->andReturn($est);
            $mock->shouldReceive('_downloadThirdPartCard')
                ->with($card->id, 'customFieldItems')
                ->andReturn($customer);
        });

        $mockedTrelloListService = new TrelloListService($mock_list);
        $list = $mockedTrelloListService->get_list($card->idList);

        //here I print the var mock if I do the DD
        $mockedTrelloMemberService = new TrelloMemberService($mock_member);
        $member = $mockedTrelloMemberService->get_member($card->idMembers);

        $mockedTrelloCardService = new TrelloCardService($mock);
        $mockedTrelloCardService->store_card($card,$member->id,$list->id);


        $card = json_decode(File::get("tests/Fixtures/card_87.json"),FALSE);
        $card_old_name = $card->name;
        $card->name = (string)rand(1,99999);


        $list = json_decode(File::get("tests/Fixtures/list_87.json"),FALSE);
        $member = json_decode(File::get("tests/Fixtures/member_87.json"),FALSE);

        $mock_list = $this->mock(TrelloListAPIService::class, function ($mock) use ($card, $list) {
            $mock->shouldReceive('_downloadListFromCard')
                ->with($card->idList)
                ->andReturn($list);
        });

        $mock_member = $this->mock(TrelloMemberAPIService::class, function ($mock) use ($card, $member) {
            $mock->shouldReceive('_downloadMemberFromCard')
                ->with($card->idMembers[0])
                ->andReturn($member);
        });

        $mock_total_time = $this->mock(TrelloCardAPIService::class, function ($mock) {
            $mock->shouldNotReceive('_downloadThirdPartCard');
        });

        $mockedTrelloListService = new TrelloListService($mock_list);
        $list = $mockedTrelloListService->get_list($card->idList);

        //here I print the var mock if I do the DD
        $mockedTrelloMemberService = new TrelloMemberService($mock_member);
        $member = $mockedTrelloMemberService->get_member($card->idMembers);

        $mockedTrelloCardService = new TrelloCardService($mock);
        $mockedTrelloCardService->store_card($card,$member->id,$list->id);

        $this->assertDatabaseHas('trello_cards',['name'=>$card_old_name,'link'=>$card->shortUrl,'total_time'=>4,'customer'=>'CAMPOS','estimate'=>1]);

    }

}
