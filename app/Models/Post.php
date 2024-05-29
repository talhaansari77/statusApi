<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'imageUrl',
        'gif',
        // 'views',
        // 'likes',
        'channelId',
        'User_id',
    ];

    public function channel()
    {
        return $this->belongsTo(StatusChannel::class,'channelId');
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    public function views()
    {
        return $this->hasMany(View::class);
    }
    public function author()
    {
        return $this->hasOne(User::class,'id','user_id');
    }


    
}
