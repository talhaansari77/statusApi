<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        "userId",
        "conversationId",
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id', "userId");
    }
    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'id', "conversationId");
    }
}
