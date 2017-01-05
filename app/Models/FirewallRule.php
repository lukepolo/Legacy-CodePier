<?php

namespace App\Models;

use App\Models\Server\Server;
use App\Models\Site\Site;
use Illuminate\Database\Eloquent\Model;

class FirewallRule extends Model
{
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'firewallRuleable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'firewallRuleable');
    }
}
