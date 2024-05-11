<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        "userId1",
        "userId2",
        'lastMessageId'
    ];

    public function lastMessage(){
        return $this->belongsTo(Message::class, 'lastMessageId');
    }
    public function user1(){
        return $this->belongsTo(User::class, 'userId1');
    }
    public function user2(){
        return $this->belongsTo(User::class, 'userId2');
    }
}
