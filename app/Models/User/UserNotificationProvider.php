<?php

namespace App\Models\User;

use App\Models\NotificationProvider;
use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserNotificationProvider extends Model
{
    use SoftDeletes, ConnectedToUser;

    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function notificationProvider()
    {
        return $this->belongsTo(NotificationProvider::class);
    }
}
