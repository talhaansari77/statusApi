<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'isModel',
        'gender',
        'birthday',
        'occupation',
        'bio',
        'link',
        'password',
        'otp',
        'userSettingsId',
        'imageUrl',
        'wallpaperUrl',
        'gif1',
        'gif2',
        'deviceId',
        'wallpaperUrl',
        'email_verified_at',
        'isActive',
        'wallComments',
        'location',
        'lat',
        'lng',
        'channelId',
        'isOnline',
        'profileType',
        'interestTags',
        'showAge',
        'orientation',
        'relationshipStatus',
        'last_seen',
        'video_url',
        
    ];

    public function settings()
    {
        return $this->hasOne(UserSettings::class, 'userId');
    }
    public function channel()
    {
        return $this->hasOne(StatusChannel::class, 'userId');
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class, 'userId');
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'favorites.userId', 'favorites.favorite');
    }
    public function favoritee()
    {
        return $this->belongsToMany(User::class, 'favorites', 'favorites.favorite', 'favorites.userId');
    }
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followings', 'followings.followee', 'followings.follower');
    }
    public function following()
    {
        return $this->belongsToMany(User::class, 'followings', 'followings.follower', 'followings.followee');
    }
    public function blockers()
    {
        return $this->belongsToMany(User::class, 'block_lists', 'block_lists.blocked', 'block_lists.blocker');
    }
    public function blocked()
    {
        return $this->belongsToMany(User::class, 'block_lists', 'block_lists.blocker', 'block_lists.blocked');
    }
    public function conversations()
    {
        return $this->belongsToMany(User::class, 'conversations', 'conversations.userId1', 'conversations.userId2');
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }


    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'otp',
        'password',
        'remember_token',
        'userSettingsId'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'isModel'=>'boolean',
            'wallComments'=>'boolean',
            'isActive'=>'boolean',
        ];
    }
}
