<?php

namespace App\Models\User;

use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    use ConnectedToUser;

    protected $guarded = ['id'];

    protected $casts = [
        'services' => 'array',
    ];
}
