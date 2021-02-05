<?php

namespace Tests\Unit;
use App\ClassU\Unirest;
use App\Services\Api\TrelloCardAPIService;
use App\Services\TrelloCardService;
use App\Traits\CardTrait;
use Illuminate\Support\Facades\File;
use Tests\TestCase;



class cardTest extends TestCase
{

    public function test_get_url_card_customFieldItems()
    {
        $cards = json_decode(File::get('tests/Fixtures/cards.json'),TRUE);
        $trelloCardAPI = new TrelloCardAPIService();
        $this->assertEquals("/cards/{$cards[0]['id']}/customFieldItems", $trelloCardAPI->_getUrlCard($cards[0]['id'], 'customFieldItems'));
    }

    public function test_get_url_card_total_time()
    {
        $cards = json_decode(File::get('tests/Fixtures/cards.json'),TRUE);
        $trelloCardAPI = new TrelloCardAPIService();
        $this->assertSame("/cards/{$cards[0]['id']}/actions", $trelloCardAPI->_getUrlCard($cards[0]['id'], 'actions'));
    }

    public function test_get_url_card_estimate()
    {
        $cards = json_decode(File::get('tests/Fixtures/cards.json'),TRUE);
        $trelloCardAPI = new TrelloCardAPIService();
        $this->assertSame("/cards/{$cards[0]['id']}/pluginData", $trelloCardAPI->_getUrlCard($cards[0]['id'], 'pluginData'));
    }



}
