<?php


namespace App\Services;


use App\Models\TrelloList;
use App\Services\Api\TrelloListAPIService;
use Illuminate\Support\Facades\Log;

class TrelloListService
{
    protected $trelloListApiService;

    public function __construct(TrelloListAPIService $trelloListApiService)
    {
        $this->trelloListApiService = $trelloListApiService;
    }

        public function get_list($listId){
        $list = TrelloList::query()->where("trello_id", $listId)->first();
        if (is_null($list)) {
//            echo "dentro if\n";
            Log::debug("Updating list");
            $res = $this->trelloListApiService->_downloadListFromCard($listId);
            $list = TrelloList::where("trello_id", $res->id)->first();

            if (is_null($list)) {
                $list = new TrelloList([
                    'trello_id' => $res->id,
                    'name' => $res->name,
                ]);

                $list->save();
            }
        }
        return $list;
    }
}
