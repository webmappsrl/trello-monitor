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

    public function get_list($list_id) {

            Log::debug("Updating list");
            $res = $this->trelloListApiService->_downloadListFromCard($list_id);
            if (!is_null($res)) {
                $list = TrelloList::where("trello_id", $res->id)->first();
                if (is_null($list))
                {
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
