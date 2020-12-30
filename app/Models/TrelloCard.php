<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrelloCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'trello_id',
        'name',
        'link'
//        'date_last_activity'
    ];

    public function trelloList() {
        return $this->belongsTo(TrelloList::class, 'list_id');
    }

    public function trelloBoard() {
        return $this->belongsTo(TrelloBoard::class, 'board_id');
    }


    public function trelloMember() {
        return $this->belongsTo(TrelloMember::class, 'member_id');
    }
}
