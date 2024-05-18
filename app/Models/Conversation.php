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
    public function archiveCon()
    {
        return $this->hasMany(Archive::class, "conversationId");
    }
    public function favoriteCon()
    {
        return $this->hasMany(FavoriteConversation::class, "conversationId");
    }
    public function trashCon()
    {
        return $this->hasMany(TrashConversation::class, "conversationId");
    }
    public function blockedCon()
    {
        return $this->hasMany(BlockChat::class, "conversationId");
    }
}
