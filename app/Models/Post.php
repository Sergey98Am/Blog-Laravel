<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'image', 'title', 'description', 'checked', 'edited', 'user_id'
    ];

    protected $dates = ['created_at', 'updated_at'];

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
}
