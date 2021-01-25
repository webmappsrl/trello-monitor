<?php

namespace Tests\Unit;

use App\Traits\TrelloAPi;
use PHPUnit\Framework\TestCase;

class trelloApiTest extends TestCase
{

    public function test_set_url_well_formed()
    {
        $trelloApi = new \App\ClassU\TrelloApi();

        dd($trelloApi->set_url());

        $this->assertSame('https://api.trello.com/1/boards/qxqVS51D/cards',$trelloApi->set_url());

    }
}
