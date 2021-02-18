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
        'customer_id',
        'estimate',
        'total_time',
        'board',
        'created_at',
        'updated_at',
        'member_id',
        'is_archived',
        'last_activity',
        'last_progress_date'
    ];


    public function trelloList() {
        return $this->belongsTo(TrelloList::class, 'list_id');
    }

//    public function trelloBoard() {
//        return $this->belongsTo(TrelloBoard::class, 'board_id');
//    }

    public function trelloMember() {
        return $this->belongsTo(TrelloMember::class, 'member_id');
    }

    public function Customer() {
        return $this->belongsTo(TrelloCustomer::class, 'customer_id');
    }
}
