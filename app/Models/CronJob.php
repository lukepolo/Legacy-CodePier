<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Model;

class CronJob extends Model
{
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'cronjobable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'cronjobable');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    public function delete()
    {
        $this->sites()->detach();
        $this->servers()->detach();
        parent::delete();
    }

    public function commandDescription($status)
    {
        return $status. ' cron job '. $this->job.' ran by '.$this->user;
    }
}
