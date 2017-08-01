<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Traits\HasServers;
use Illuminate\Database\Eloquent\Model;

class Schema extends Model
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
        return $this->morphedByMany(Site::class, 'schemable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'schemable');
    }

    public function users()
    {
        return $this->hasMany(SchemaUser::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function commandDescription($status)
    {
        return $status.' database '.$this->name;
    }
}
