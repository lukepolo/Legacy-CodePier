<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Traits\HasServers;
use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Model;

class FirewallRule extends Model
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
        return $this->morphedByMany(Site::class, 'firewallRuleable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'firewallRuleable');
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
        if ('*' === $this->port) {
            $firewallDescription = ' all ports ('.$this->description.') to '.$this->from_ip;
        } else {
            $to = ' ';

            if ($this->from_ip) {
                $to = ' to '.$this->from_ip;
            }

            $firewallDescription = 'port ('.$this->description.') '.$this->port.'/'.$this->type.' '.$to;
        }

        return $status.' '.$firewallDescription;
    }
}
