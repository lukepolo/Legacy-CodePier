<?php

namespace App\Models\User;

use App\Traits\ConnectedToUser;
use App\Models\NotificationSetting;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    use ConnectedToUser;

    protected $guarded = ['id'];

    protected $casts = [
        'services' => 'array',
    ];

    public function setting()
    {
        return $this->belongsTo(NotificationSetting::class, 'notification_setting_id', 'id');
    }
}
