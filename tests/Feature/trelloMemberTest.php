<?php

namespace Tests\Feature;

use App\Services\Api\TrelloMemberAPIService;
use App\Services\TrelloMemberService;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;

use Tests\TestCase;

class trelloMemberTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_member()
    {
        $card = json_decode(File::get("tests/Fixtures/card_59.json"),FALSE);
        $member = json_decode(File::get("tests/Fixtures/member_59.json"),FALSE);

        $mock_member = $this->mock(TrelloMemberAPIService::class, function ($mock) use ($card, $member) {
            $mock->shouldReceive('_downloadMemberFromCard')
                ->with($card->idMembers[0])
                ->once()
                ->andReturn($member);
        });

        $mockedTrelloMemberService = new TrelloMemberService($mock_member);
        $member = $mockedTrelloMemberService->get_member($card->idMembers);

        $this->assertDatabaseHas('trello_members',['name'=>$member->name,'trello_id'=>$member->trello_id]);
        \App\Models\TrelloMember::destroy($member->id);
    }
}
