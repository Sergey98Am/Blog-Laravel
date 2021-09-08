<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    protected $fillable = [
        'image',
        'title',
        'description',
        'checked',
        'edited',
        'user_id'
    ];

    protected $dates = ['created_at', 'updated_at'];

    protected $appends = ['liked_by_auth_user'];

    public function getUpdatedAtAttribute($date)
    {
        if ($this->edited) {
            return Carbon::parse($date)->diffForHumans();
        }
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function getLikedByAuthUserAttribute()
    {
        if ($user = Auth::user()) {
            $like = $this->likes()->where('user_id', $user->id)->first();

            if ($like) {
                return true;
            }

            return false;
        }
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->where('parent_id', null);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }
}
