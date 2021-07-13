<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JWTAuth;

class Post extends Model
{
    protected $fillable = [
        'image', 'title', 'description', 'checked', 'edited', 'user_id'
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

    public function getLikedByAuthUserAttribute()
    {
        if (JWTAuth::user()) {
            $like = $this->likes()->where('user_id', JWTAuth::user()->id)->first();

            if ($like) {
                return true;
            }

            return false;
        }
    }

    public function likes()
    {
        return $this->hasMany('App\Models\Like');
    }
}
