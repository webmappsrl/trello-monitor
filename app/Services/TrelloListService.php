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

        $list = TrelloList::query()->where("trello_id", "=", $list_id)->first();

        if (is_null($list)) {
            Log::debug("Updating list");

            $res = $this->trelloListApiService->_downloadListFromCard($list_id);
            if (!is_null($res)) {
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
