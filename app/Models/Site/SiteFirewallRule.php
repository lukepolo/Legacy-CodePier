<?php

namespace App\Models\Site;

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
}
