<?php


namespace App\Services;


use App\Models\TrelloMember;
use App\Services\Api\TrelloMemberAPIService;
use Illuminate\Support\Facades\Log;

class TrelloMemberService
{
    protected $trelloMemberApiService;

    public function __construct(TrelloMemberAPIService $trelloMemberApiService)
    {
        $this->trelloMemberApiService = $trelloMemberApiService;
    }

        public function get_member($member1) {
        if (count($member1)>0 && is_array($member1))
        {
            $member = TrelloMember::query()->where("trello_id", "=", $member1[0])->first();
            if (is_null($member)) {
                Log::debug("Updating member");
                $res = $this->trelloMemberApiService->_downloadMemberFromCard($member1[0]);
                if (!is_null($res)) {

                    $member = new TrelloMember([
                        'trello_id' => $res->id,
                        'name' => $res->fullName,
                    ]);
                    $member->save();
                }
            }
        }
        else
        {
            $member =
            '';
        }


        return $member;
    }
}
