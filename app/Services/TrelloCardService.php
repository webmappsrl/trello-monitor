<?php


namespace App\Services;

use App\Models\TrelloCard;
use App\Services\Api\TrelloCardAPIService;

class TrelloCardService
{
    protected $trelloCardApiService;

    public function __construct(TrelloCardAPIService $trelloCardApiService)
    {
        $this->trelloCardApiService = $trelloCardApiService;
    }

    public function setCustomer($card_id) {
        //TODO: mettere in una var di configurazione .ENV
        $customer_key =env('CUSTOMER_KEY');

        $res = $this->trelloCardApiService->_downloadThirdPartCard($card_id,'customFieldItems');
        $fields = array();
        $cf = '';
        if (is_array($res) && count($res)>0)
        {
            foreach ($res as $re)
            {
                $fields[$re->idCustomField] = $re;
            }
            if (array_key_exists($customer_key,$fields))
            {
                $cf = $fields[$customer_key]->value->text;
            }
        }
        return $cf;


    }

    public function total_time($card_id)
    {
        $total_time = $this->trelloCardApiService->_downloadThirdPartCard($card_id,'actions');
        $min = 0;
        if (is_array($total_time) && count($total_time)>0)
        {
            for ($i = count($total_time)-1; $i > 0; $i--)
            {
                if (isset($total_time[$i]->data->listAfter->name))
                {
                    if ($total_time[$i]->data->listAfter->name == 'PROGRESS')
                    {
                        $minutes = abs(strtotime($total_time[$i]->date) - time()) / 60;
                        $minutes1 = abs(strtotime($total_time[$i-1]->date) - time()) / 60;
                        $min += $minutes - $minutes1;
                    }
                }
            }
        }
        else $min = 0;
        return round($min);

    }

    public function last_date($card)
    {
        $data = $this->trelloCardApiService->_downloadThirdPartCard($card->id,'actions');
        if (count($data)>0 && is_array($data))
        {
            $itt = $data[count($data)-1]->date;
        }
        else $itt = $card->dateLastActivity;

        return $itt;

    }

    public function estimate($card_id)
    {
        $estimate = $this->trelloCardApiService->_downloadThirdPartCard($card_id,'pluginData');

        if (is_array($estimate) && count($estimate)>0)
        {

            if (isset($estimate[0]) && strlen($estimate[0]->value) > 0 && strpos($estimate[0]->value, 'estimate') ) {
                $estimate = explode("\"", $estimate[0]->value);
                $estimate = $estimate[3];
            }
            else $estimate = 0;

        }
        else  $estimate = 0;

        return (int) $estimate;

    }

    public function store_card($card, $total_time,$estimate,$customer,$itt)
    {
        // find the card
        $dbCard = TrelloCard::query()->where("trello_id", "=", $card->id)->first();
//        echo "nedo ".$dbCard."\n";
        if(is_null($dbCard)) {
//            echo "inserisco\n";
            // NON esiste: lo inserisco sicuramente
            //create
            $newCard = new TrelloCard([
                'trello_id' => $card->id,
                'name' => $card->name,
                'link' => $card->shortUrl,
                'total_time'=> $total_time,
                'estimate'=>$estimate,
                'customer'=>$customer,
                'created_at'=>$itt,
                'updated_at'=>$card->dateLastActivity
            ]);

            $newCard->save();

        } else {
            if(strtotime($dbCard->updated_at)<strtotime($card->dateLastActivity)) {
                //update
                $dbCard->name=$card->name;
                $dbCard->total_time=$total_time;
                $dbCard->estimate=$estimate;
                $dbCard->customer=$customer;
                $dbCard->updated_at = $card->dateLastActivity;
                $dbCard->save();
            }
        }
    }

}
