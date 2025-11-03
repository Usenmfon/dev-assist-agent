<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevAssist extends Model
{
    protected $fillable = [
        'channel_id', 'user_id', 'message', 'response', 'intent',
    ];
}
