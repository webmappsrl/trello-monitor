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

    public function get_customer($card_id) {
        $customer_key = env('CUSTOMER_KEY');

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

    public function get_total_time($card_id)
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

    public function get_estimate($card_id)
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

    public function store_card($card,$member_id,$list_id)
    {
        // find the card
        $dbCard = TrelloCard::query()->where("trello_id", "=", $card->id)->first();

        if(is_null($dbCard)) {

            $total_time = $this->get_total_time($card->id);
            $estimate = $this->get_estimate($card->id);
            $customer = $this->get_customer($card->id);

            // NON esiste: lo inserisco sicuramente
            $dbCard = new TrelloCard([
                'trello_id' => $card->id,
                'name' => $card->name,
                'link' => $card->shortUrl,
                'total_time'=> $total_time,
                'estimate'=>$estimate,
                'customer'=>$customer,
            ]);

            $dbCard->save();
            if ($member_id!='')
            {
                $dbCard->member_id = $member_id;
                $dbCard->save();
            }
            if ($list_id!='')
            {
                $dbCard->list_id = $list_id;
                $dbCard->save();
            }

        } else {
            if(strtotime($dbCard->updated_at)<strtotime($card->dateLastActivity)) {
                $total_time = $this->get_total_time($card->id);
                $estimate = $this->get_estimate($card->id);
                $customer = $this->get_customer($card->id);

                //update
                $dbCard->name=$card->name;
                $dbCard->total_time=$total_time;
                $dbCard->estimate=$estimate;
                $dbCard->customer=$customer;
                $dbCard->member_id = $member_id;
                $dbCard->list_id = $list_id;

                $dbCard->save();
            }
        }
        return $dbCard;
    }

}
