<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        "message",
        "gif",
        "senderId",
        "receiverId",
        "conversationId",
    ];

    public function sender(){
        return $this->belongsTo(User::class, 'senderId');
    }
    public function receiver(){
        return $this->belongsTo(User::class, 'receiverId');
    }
    public function attachments(){
        return $this->hasMany(MessageAttachment::class, 'messageId');
    }
}
