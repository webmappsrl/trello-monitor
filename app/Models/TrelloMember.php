<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrelloMember extends Model
{
    use HasFactory;

    protected $fillable = ["trello_id", "name"];

    public function trelloCards() {
        return $this->hasMany(TrelloCard::class);
    }
}
