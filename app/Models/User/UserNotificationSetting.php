<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'services' => 'array'
    ];
}
