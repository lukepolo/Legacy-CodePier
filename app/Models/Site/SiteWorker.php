<?php

namespace App\Models\Site;

use App\Models\Server\ServerWorker;
use App\Traits\ConnectedToUser;
use App\Traits\FireEvents;
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
