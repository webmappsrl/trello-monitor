<?php


namespace App\ClassU;

use App\Traits\CardTrait;
use Unirest\Request;


class Unirest
{
    use CardTrait;

    public function get_data(string $url) {
        if (empty(env('TRELLO_KEY')) || empty(env('TRELLO_TOKEN'))) {
            throw new \Exception("Configuration missing: TRELLO_KEY and/or TRELLO_TOKEN are mandatory");
        }
        $headers = array('Accept' => 'application/json');
        $query = array('key' => env('TRELLO_KEY'), 'token' => env('TRELLO_TOKEN'));
        $r = Request::get($url, $headers, $query);
        return $r->body;
    }

}

