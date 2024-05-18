<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InNotification extends Model
{
    use HasFactory;
    protected $fillable = [
        "imageUrl",
        "username",
        "description",
        "senderId",
        "receiverId",
        "forFollow",
        "forComment",
    ];

    public function sender(){
        return $this->belongsTo(User::class, "senderId");
    }
    public function receiver(){
        return $this->belongsTo(User::class, "receiverId");
    }
}
