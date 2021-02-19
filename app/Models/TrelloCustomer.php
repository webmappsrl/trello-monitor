<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrelloCustomer extends Model
{
    use HasFactory;

    protected $fillable = ["trello_id", "name","last_activity_progress","cards","todo","done"];

    protected $casts = [
        'last_activity_progress' => 'datetime'
    ];
    public function trelloCards() {
        return $this->hasMany(TrelloCard::class,'customer_id');
    }
}
