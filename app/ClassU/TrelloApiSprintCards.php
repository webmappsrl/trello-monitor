<?php


namespace App\ClassU;


class TrelloApiSprintCards extends TrelloApi
{
    public function __construct()
    {

    }

    public function set_cards()
    {
        return $this->call($this->set_url());
    }

}
