<?php

namespace App\Models\User;

use App\Traits\ConnectedToUser;
use App\Models\NotificationProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserNotificationProvider extends Model
{
    use SoftDeletes, ConnectedToUser;

    protected $guarded = ['id'];

    protected $dates = [
        'created_at',
        'updated_at',
        'expires_in',
        'deleted_at'
    ];

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
