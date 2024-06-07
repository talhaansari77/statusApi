<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'imageUrl',
        'username',
        'description',
        'userId',
        'commentatorId',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function commentator()
    {
        return $this->belongsTo(User::class, 'commentatorId');
    }

    protected $hidden = [
        // 'commentatorId',
    ];
}
