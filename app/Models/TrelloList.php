<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrelloList extends Model
{
    use HasFactory;

    public function trelloCards() {
        return $this->hasMany(TrelloCard::class);
    }
}
