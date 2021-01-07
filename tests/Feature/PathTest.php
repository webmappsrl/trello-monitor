<?php

namespace Tests\Feature;

use App\Http\Controllers\TrelloCardController;
use App\Models\TrelloCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Console\Commands\sync;
use Unirest\Request;


class PathTest extends TestCase
{
    public function test_get_url_card_customFieldItems()
    {
        $tcc = new TrelloCardController();
        $cards = json_decode(Storage::get('test_data/cards.json'),TRUE);
        $this->assertSame("/cards/{$cards[0]['id']}/customFieldItems", $tcc->_getUrlCard($cards[0]['id'], 'customFieldItems'));
    }

    public function test_get_url_card_total_time()
    {
        $tcc = new TrelloCardController();
        $cards = json_decode(Storage::get('test_data/cards.json'),TRUE);
        $this->assertSame("/cards/{$cards[0]['id']}/actions", $tcc->_getUrlCard($cards[0]['id'], 'actions'));
    }

    public function test_get_url_card_estimate()
    {
        $tcc = new TrelloCardController();
        $cards = json_decode(Storage::get('test_data/cards.json'),TRUE);
        $this->assertSame("/cards/{$cards[0]['id']}/pluginData", $tcc->_getUrlCard($cards[0]['id'], 'pluginData'));

    }

    public function test_get_wrong_filter()
    {
        $tcc = new TrelloCardController();
        $cards = json_decode(Storage::get('test_data/cards.json'),TRUE);
        $this->assertSame("select the correct filter: customFieldItemsxxx", $tcc->_downloadCard($cards[0]['id'], 'customFieldItemsxxx'));

    }

    public function test_get_storage_card_estimate()
    {
        $tcc = new TrelloCardController();
        $cards = json_decode(Storage::get('test_data/cards.json'),TRUE);
        $this->assertSame("test_data/pluginData{$cards[0]['id']}.json", $tcc->_getStorageCard($cards[0]['id'], 'pluginData'));
    }
}
