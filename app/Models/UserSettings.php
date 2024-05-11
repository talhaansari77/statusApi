<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'privacyLvl',
        'blockedAccounts',
        'pushNotification',
        'instagramLink',
        'youtubeLink',
        'userId',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    
}
