<?php

namespace App\Traits;

use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\DB;
use Unirest\Request;

trait CardTrait {

    public function _getUrlCard(string $cardId, string $filter)
    {
        $url = "/cards/{$cardId}/{$filter}";
        return $url;
    }

    public function _getStorageCard(string $cardId, string $filter)
    {
        $url = "test_data/{$filter}{$cardId}.json";
        return $url;
    }

    public function get_data(string $url) {
        if (empty(env('TRELLO_KEY')) || empty(env('TRELLO_TOKEN'))) {
            throw new \Exception("Configuration missing: TRELLO_KEY and/or TRELLO_TOKEN are mandatory");
        }
        $headers = array('Accept' => 'application/json');
        $query = array('key' => env('TRELLO_KEY'), 'token' => env('TRELLO_TOKEN'));
        $r = Request::get($url, $headers, $query);
        return $r->body;
    }

    public function _downloadCard(string $cardId, string $filter) {
        $url = TRELLO_API_BASE_URL . $this->_getUrlCard($cardId, $filter);
        $res = $this->get_data($url);
        return $res;
    }

    //calc card time in -> progress ->
    public function totalTime($total_time)
    {
        $min = 0;
        if (is_array($total_time))
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
        return $min;
    }

    public function last_date($data, $card)
    {
        if (count($data)>0 && is_array($data))
        {
            $itt = $data[count($data)-1]->date;
        }
        else $itt = $card->dateLastActivity;

        return $itt;

    }



    public function estimate($estimate)
    {
        if (is_array($estimate) && count($estimate)>0)
        {

            if (isset($estimate[0]) && strlen($estimate[0]->value) > 0 && strpos($estimate[0]->value, 'estimate') ) {
                $estimate = explode("\"", $estimate[0]->value);
                $estimate = $estimate[3];
            }
            else $estimate = 0;

        }
        else  $estimate = 0;

        return $estimate;

    }


}

