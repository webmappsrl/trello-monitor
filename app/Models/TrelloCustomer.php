<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrelloCustomer extends Model
{
    use HasFactory;

    protected $fillable = ["trello_id", "name"];

    public function trelloCards() {
        return $this->hasMany(TrelloCard::class,'customer_id');
    }
}
