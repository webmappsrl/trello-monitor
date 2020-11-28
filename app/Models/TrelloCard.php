<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrelloCard extends Model
{
    use HasFactory;

    public function trelloList() {
        return $this->belongsTo(TrelloList::class);
    }

    public function trelloBoard() {
        return $this->belongsTo(TrelloBoard::class);
    }

    public function trelloMember() {
        return $this->belongsTo(TrelloMember::class);
    }
}
