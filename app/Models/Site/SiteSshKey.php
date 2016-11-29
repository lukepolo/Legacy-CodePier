<?php

namespace App\Models\Site;

use App\Models\Server\ServerSshKey;
use App\Traits\ConnectedToUser;
use App\Traits\FireEvents;
use Illuminate\Database\Eloquent\Model;

class SiteSshKey extends Model
{
    use FireEvents, ConnectedToUser;

    static $userModel = 'site';

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
