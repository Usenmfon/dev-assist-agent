<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevAssist extends Model
{
    protected $fillable = [
        'message', 'response', 'intent',
    ];
}
