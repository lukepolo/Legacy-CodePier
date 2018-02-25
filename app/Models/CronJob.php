<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Traits\HasServers;
use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Model;

class CronJob extends Model
{
    use HasServers;

    protected $guarded = ['id'];

    protected $casts = [
        'server_ids' => 'array',
        'server_types' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'cronJobable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'cronJobable');
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
        return $status.' cron job '.$this->job.' ran by '.$this->user;
    }
}
