<?php

namespace App\Models\Site;

use App\Traits\FireEvents;
use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;
use App\Models\Server\ServerFirewallRule;

class SiteFirewallRule extends Model
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

    public function serverFirewallRules()
    {
        return $this->hasMany(ServerFirewallRule::class);
    }
}
