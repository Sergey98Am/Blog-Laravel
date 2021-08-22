<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    protected $hidden = [
        'id',
        'for_admin'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
