<?php


namespace App\ClassU;


use App\Models\TrelloCard;
use Illuminate\Support\Facades\Log;

class downloadCard extends Unirest
{

    public function _downloadCard(string $cardId, string $filter)
    {
        echo "API downloadCard!\n";
        $url = TRELLO_API_BASE_URL . $this->_getUrlCard($cardId, $filter);
        $res = $this->get_data($url);
        return $res;
    }

    public function _downloadCardsFromBoard(string $boardId) {
        echo "API downloadCards!\n";
        $url = TRELLO_API_BASE_URL . "/boards/{$boardId}/cards";
        $res = $this->get_data($url);
        return $res;
    }

    public function _downloadCardsArchive(string $boardId, string $filter) {
        $url = TRELLO_API_BASE_URL . "/boards/{$boardId}/cards/{$filter}";
        $res = $this->get_data($url);
        return $res;
    }

    public function createCard($card)
    {
            // find the card
            $dbCard = TrelloCard::query()->where("trello_id", "=", $card->id)->first();
            if(is_null($dbCard)) {
                // NON esiste: lo inserisco sicuramente
                $newCard = new TrelloCard([
                    'trello_id' => $card->id,
                    'name' => $card->name,
                    'link' => $card->shortUrl
                ]);
                $newCard->save();
            } else {
                if(strtotime($dbCard->updated_at)<strtotime($card->dateLastActivity)) {
                    $dbCard->name=$card->name;
                    $dbCard->save();
                }
            }
    }

}
