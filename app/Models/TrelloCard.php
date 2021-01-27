<?php

namespace App\Models;

use App\ClassU\downloadCard;
use App\Traits\CardTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrelloCard extends Model
{
    use HasFactory;


    protected $fillable = [
        'trello_id',
        'name',
        'link',
        'customer',
        'estimate',
        'total_time',
        'board',
        'created_at',
        'updated_at',
        'member_id'
    ];






//    public function setEstimate() {
//        $j = new downloadCard();
//        $res = $j->_downloadCard($this->trello_id,'pluginData');
//        return $res;
//    }
//
//    public function setEstimateValidate($array_customer) {
//        $j = new downloadCard();
//        $estimate = $j->estimate($array_customer);
//        $this->estimate = $estimate;
//        return $this->estimate;
//    }
//
//    public function setTotalTime() {
//        $j = new downloadCard();
//        $res = $j->_downloadCard($this->trello_id,'actions');
//        return $res;
//    }
//
//    public function setTotalTimeValidate($array_customer) {
//        $j = new downloadCard();
//        $total_time = $j->totalTime($array_customer);
//        $this->total_time = $total_time;
//        return $this->total_time;
//    }

    public function trelloList() {
        return $this->belongsTo(TrelloList::class, 'list_id');
    }

//    public function trelloBoard() {
//        return $this->belongsTo(TrelloBoard::class, 'board_id');
//    }

    public function trelloMember() {
        return $this->belongsTo(TrelloMember::class, 'member_id');
    }
}
