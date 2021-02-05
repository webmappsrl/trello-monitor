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

//    public function get_member($member_by_api)
//    {
//        if (count($member_by_api) > 0 && is_array($member_by_api)) {
//            $member = TrelloMember::where("trello_id", $member_by_api)->first();
//
//            if (is_null($member)) {
//                $res = $this
//                    ->trelloMemberApiService
//                    ->_downloadMemberFromCard($member_by_api[0]);
//
//                $member = new TrelloMember(['trello_id' => $res->id, 'name' => $res->fullName,]);
//                $member->save();
//
//            } else {
//                $member = $member->id;
//            }
//        }
//        return $member;
//    }


    public function get_members($memberId) {
//        print_r($memberId);
//        echo "count = ".count($memberId)."\n";

        if (is_array($memberId) && count($memberId)>0)
        {
            echo "memberId = ".$memberId[0]."\n";
            $res = $this
                ->trelloMemberApiService
                ->_downloadMemberFromCard($memberId[0]);
            print_r($res);

            $member = TrelloMember::where("trello_id", "=", $res->id)->first();

            if (is_null($member)) {
                Log::debug("Updating member");
                if (isset($memberId[0]))
                {
//                $res = $this
//                    ->trelloMemberApiService
//                    ->_downloadMemberFromCard($memberId[0]);

                    $member = new TrelloMember([
                        'trello_id' => $res->id,
                        'name' => $res->fullName,
                    ]);

                    $member->save();


                }

            }

        }
        else $member ='';

        return $member;

    }

        public function get_member($memberId) {
            $member = TrelloMember::query()->where("trello_id",$memberId)->first();

            if (is_array($memberId) && count($memberId)>0)
            {
//                echo "res = ".$member."\n";
//                echo "is_null(member) = ".is_null($member)."\n";
                if (is_null($member)) {
                    Log::debug("Updating member");
                    $res = $this
                        ->trelloMemberApiService
                        ->_downloadMemberFromCard($memberId[0]);

                    if (!is_null($res) ) {
                        $member = new TrelloMember([
                            'trello_id' => $res->id,
                            'name' => $res->fullName,
                        ]);

                        $member->save();
                    }
                }

            }

        return $member;
    }


}

