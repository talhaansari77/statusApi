<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusChannel extends Model
{
    use HasFactory;

    protected $fillable=[
        'channelName',
        'lastPostId',
        'userId'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'channelId');
    }

    public function lastPost()
    {
        return $this->hasOne(Post::class,'id','lastPostId');
    }
}
