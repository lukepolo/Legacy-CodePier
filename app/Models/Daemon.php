<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Traits\HasServers;
use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Model;

class Daemon extends Model
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
        return $this->morphedByMany(Site::class, 'daemonable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'daemonable');
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
        return $status.' daemon running '.$this->command.' ran by '.$this->user;
    }
}
