<?php

namespace App\Models\Site;

use App\Traits\FireEvents;
use App\Traits\ConnectedToUser;
use App\Models\Server\ServerWorker;
use Illuminate\Database\Eloquent\Model;

class SiteWorker extends Model
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

    public function serverWorkers()
    {
        return $this->hasMany(ServerWorker::class);
    }
}
