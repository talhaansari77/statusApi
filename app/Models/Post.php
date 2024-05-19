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


    
}
