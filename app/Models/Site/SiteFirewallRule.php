<?php

namespace App\Models\Site;

use App\Models\Server\ServerFirewallRule;
use App\Traits\ConnectedToUser;
use App\Traits\FireEvents;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(ServerFirewallRule::class)->where('progress', '>=', '100');
    }
}
