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

        public function get_member($member_by_api) {
        if (count($member_by_api)>0 && is_array($member_by_api))
        {
                Log::debug("Updating member");
                $res = $this->trelloMemberApiService->_downloadMemberFromCard($member_by_api[0]);
            var_dump($res);

            if (!is_null($res)) {
                    $member = TrelloMember::where("trello_id", $res->id)->first();
                    if (is_null($member))
                    {
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
