<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use App\ClassU\downloadCard;

class insertUpdateCardTest extends TestCase
{
    use App\ClassU\downloadCard;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_insert_card()
    {
        $r = new downloadCard();
        $card = json_decode(File::get("tests/test_data/card1.json"),FALSE);

        $card = $r->createCard($card);
        dd($card);
    }
}
