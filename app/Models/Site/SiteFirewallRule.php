<?php

namespace App\Models\Site;

use App\Models\Server\ServerFirewallRule;
use Illuminate\Database\Eloquent\Model;

class SiteFirewallRule extends Model
{
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
