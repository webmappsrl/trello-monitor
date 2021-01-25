<?php

namespace Tests\Unit;

use App\ClassU\TrelloApiSprintCards;
use PHPUnit\Framework\TestCase;

class TrelloApiSprintsCardsTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $r = new TrelloApiSprintCards();
        dd($r->set_cards());
    }
}
