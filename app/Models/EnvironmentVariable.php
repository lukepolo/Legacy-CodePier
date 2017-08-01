<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Traits\HasServers;
use Illuminate\Database\Eloquent\Model;

class EnvironmentVariable extends Model
{
    use HasServers;

    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'environmentable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'environmentable');
    }

    /*
     |--------------------------------------------------------------------------
     | Helpers
     |--------------------------------------------------------------------------
     */

    public function commandDescription($status)
    {
        return $status.' environment variable '.$this->variable;
    }
}
