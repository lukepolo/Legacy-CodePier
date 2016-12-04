<?php

namespace App\Models\Site;

use App\Traits\FireEvents;
use App\Traits\ConnectedToUser;
use App\Models\Server\ServerSshKey;
use Illuminate\Database\Eloquent\Model;

class SiteSshKey extends Model
{
    use FireEvents, ConnectedToUser;

    public static $userModel = 'site';

    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function serverSshKeys()
    {
        return $this->hasMany(ServerSshKey::class);
    }
}
