<?php

namespace Tests\Unit;
use App\ClassU\Unirest;
use App\Traits\CardTrait;
use Illuminate\Support\Facades\File;
use Tests\TestCase;



class cardTest extends TestCase
{
    use CardTrait;

    protected $card;

    public function test_get_url_card_customFieldItems()
    {
        $cards = json_decode(File::get('tests/test_data/cards.json'),TRUE);
//        dd($cards[0]['id']);
        $this->assertEquals("/cards/{$cards[0]['id']}/customFieldItems", $this->_getUrlCard($cards[0]['id'], 'customFieldItems'));
    }

    public function test_get_url_card_total_time()
    {
        $cards = json_decode(File::get('tests/test_data/cards.json'),TRUE);
        $this->assertSame("/cards/{$cards[0]['id']}/actions", $this->_getUrlCard($cards[0]['id'], 'actions'));
    }

    public function test_get_url_card_estimate()
    {
        $cards = json_decode(File::get('tests/test_data/cards.json'),TRUE);
        $this->assertSame("/cards/{$cards[0]['id']}/pluginData", $this->_getUrlCard($cards[0]['id'], 'pluginData'));
    }

    public function test_get_storage_card_estimate()
    {
        $cards = json_decode(File::get('tests/test_data/cards.json'),TRUE);
        $this->assertSame("test_data/pluginData{$cards[0]['id']}.json", $this->_getStorageCard($cards[0]['id'], 'pluginData'));
    }

    public function test_total_time_card_in_progress()
    {
        $actionCard = json_decode(File::get("tests/test_total_time/total_time_1.json"),FALSE);
        $calcTime  = round($this->totalTime($actionCard));
        $calcTimeAction = 51.0;
        $this->assertSame($calcTime,$calcTimeAction);

        $actionCard = json_decode(File::get("tests/test_total_time/total_time_2.json"),FALSE);
        $calcTime  = round($this->totalTime($actionCard));
        $calcTimeAction = 44.0;
        $this->assertSame($calcTime,$calcTimeAction);

        //card non messa in progress
        $actionCard = json_decode(File::get("tests/test_total_time/total_time_3.json"),FALSE);
        $calcTime  = round($this->totalTime($actionCard));
        $calcTimeAction = 0.0;
        $this->assertSame($calcTime,$calcTimeAction);

        //card messa in to be tested direttamente
        $actionCard = json_decode(File::get("tests/test_total_time/total_time_4.json"),FALSE);
        $calcTime  = round($this->totalTime($actionCard));
        $calcTimeAction = 0.0;
        $this->assertSame($calcTime,$calcTimeAction);

        $actionCard = json_decode(File::get("tests/test_total_time/total_time_5.json"),FALSE);
        $calcTime  = round($this->totalTime($actionCard));
        $calcTimeAction = 7.0;
        $this->assertSame($calcTime,$calcTimeAction);

        $actionCard = json_decode(File::get("tests/test_total_time/total_time_6.json"),FALSE);
        $calcTime  = round($this->totalTime($actionCard));
        $calcTimeAction = 18.0;
        $this->assertSame($calcTime,$calcTimeAction);

        $actionCard = json_decode(File::get("tests/test_total_time/total_time_7.json"),FALSE);
        $calcTime  = round($this->totalTime($actionCard));
        $calcTimeAction = 88.0;
        $this->assertSame($calcTime,$calcTimeAction);

        $actionCard = json_decode(File::get("tests/test_total_time/total_time_8.json"),FALSE);
        $calcTime  = round($this->totalTime($actionCard));
        $calcTimeAction = 11.0;
        $this->assertSame($calcTime,$calcTimeAction);

        $actionCard = json_decode(File::get("tests/test_total_time/total_time_9.json"),FALSE);
        $calcTime  = round($this->totalTime($actionCard));
        $calcTimeAction = 239.0;
        $this->assertSame($calcTime,$calcTimeAction);
    }

    public function test_custom_field()
    {
        $cf = json_decode(File::get("tests/test_data/cf_1.json"),FALSE);
        $custom_field = $this->custom_field($cf);
        $this->assertSame($custom_field,$cf[2]->value->text);

        $cf = json_decode(File::get("tests/test_data/cf_2.json"),FALSE);
        $custom_field = $this->custom_field($cf);
        $this->assertSame($custom_field,$cf[1]->value->text);

        $cf = json_decode(File::get("tests/test_data/cf_3.json"),FALSE);
        $custom_field = $this->custom_field($cf);
        $this->assertSame($custom_field,$cf[1]->value->text);

        $cf = json_decode(File::get("tests/test_data/cf_4.json"),FALSE);
        $custom_field = $this->custom_field($cf);
        $this->assertSame($custom_field,$cf[1]->value->text);

        $cf = json_decode(File::get("tests/test_data/cf_5.json"),FALSE);
        $custom_field = $this->custom_field($cf);
        $this->assertSame($custom_field,$cf[1]->value->text);

        $cf = json_decode(File::get("tests/test_data/cf_6.json"),FALSE);
        $custom_field = $this->custom_field($cf);
        $this->assertSame($custom_field,$cf[1]->value->text);

        $cf = json_decode(File::get("tests/test_data/cf_7.json"),FALSE);
        $custom_field = $this->custom_field($cf);
        $this->assertSame($custom_field,$cf[1]->value->text);

        $cf = json_decode(File::get("tests/test_data/cf_8.json"),FALSE);
        $custom_field = $this->custom_field($cf);
        $this->assertSame($custom_field,$cf[1]->value->text);

        $cf = json_decode(File::get("tests/test_data/cf_9.json"),FALSE);
        $custom_field = $this->custom_field($cf);
        $this->assertSame($custom_field,$cf[1]->value->text);

        $cf = json_decode(File::get("tests/test_data/cf_10.json"),FALSE);
        $custom_field = $this->custom_field($cf);
        $this->assertSame($custom_field,$cf[1]->value->text);
    }

    public function test_estimate()
    {
        $estimate = json_decode(File::get("tests/test_data/estimate_1.json"),FALSE);
        $estimate = $this->estimate($estimate);
        $this->assertSame($estimate,'1');

        $estimate = json_decode(File::get("tests/test_data/estimate_2.json"),FALSE);
        $estimate = $this->estimate($estimate);
        $this->assertSame($estimate,'1');

        $estimate = json_decode(File::get("tests/test_data/estimate_3.json"),FALSE);
        $estimate = $this->estimate($estimate);
        $this->assertSame($estimate,'1');

        $estimate = json_decode(File::get("tests/test_data/estimate_4.json"),FALSE);
        $estimate = $this->estimate($estimate);
        $this->assertSame($estimate,'1');

        $estimate = json_decode(File::get("tests/test_data/estimate_5.json"),FALSE);
        $estimate = $this->estimate($estimate);
        $this->assertSame($estimate,'1');

        $estimate = json_decode(File::get("tests/test_data/estimate_6.json"),FALSE);
        $estimate = $this->estimate($estimate);
        $this->assertSame($estimate,'1');

        $estimate = json_decode(File::get("tests/test_data/estimate_7.json"),FALSE);
        $estimate = $this->estimate($estimate);
        $this->assertSame($estimate,'1');

        $estimate = json_decode(File::get("tests/test_data/estimate_8.json"),FALSE);
        $estimate = $this->estimate($estimate);
        $this->assertSame($estimate,'1');

        $estimate = json_decode(File::get("tests/test_data/estimate_9.json"),FALSE);
        $estimate = $this->estimate($estimate);
        $this->assertSame($estimate,'1');

        $estimate = json_decode(File::get("tests/test_data/estimate_10.json"),FALSE);
        $estimate = $this->estimate($estimate);
        $this->assertSame($estimate,'1');
    }

    public function test_last_date()
    {
        $total_time = json_decode(File::get("tests/test_total_time/total_time_9.json"),FALSE);
        $card = json_decode(File::get("tests/test_data/card1.json"),FALSE);
        $date = $this->last_date($total_time,$card);
        $this->assertSame($date,"2020-11-18T08:02:55.236Z");

        $total_time = json_decode(File::get("tests/test_total_time/total_time_4.json"),FALSE);
        $card = json_decode(File::get("tests/test_data/card1.json"),FALSE);
        $date = $this->last_date($total_time,$card);
        $this->assertSame($date,$card->dateLastActivity);
    }

}
