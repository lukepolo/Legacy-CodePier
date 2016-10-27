<?php

namespace App\Models\Site;

use App\Models\Server\Server;
use App\Models\Server\ServerWorker;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteWorker.
 */
class SiteWorker extends Model
{
    protected $guarded = ['id'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function serverWorkers()
    {
        return $this->hasMany(ServerWorker::class);
    }

    public function server()
    {
        return $this->hasOne(Server::class);
    }
}
