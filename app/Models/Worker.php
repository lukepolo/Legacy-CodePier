<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Traits\HasServers;
use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
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
        return $this->morphedByMany(Site::class, 'workerable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'workerable');
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
        return $status.' '.$this->number_of_workers.' workers running '.$this->command.' ran by '.$this->user;
    }
}
