<?php

namespace App\Models\Site;

use App\Models\Server\ServerCronJob;
use App\Traits\ConnectedToUser;
use App\Traits\FireEvents;
use Illuminate\Database\Eloquent\Model;

class SiteCronJob extends Model
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

    public function serverCronJobs()
    {
        return $this->hasMany(ServerCronJob::class);
    }
}
