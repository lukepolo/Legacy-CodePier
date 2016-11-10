<?php

namespace App\Models\Site;

use App\Traits\FireEvents;
use Illuminate\Database\Eloquent\Model;

class SiteDeployment extends Model
{
    use FireEvents;

    protected $guarded = ['id'];

    protected $casts = [
        'log' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function serverDeployments()
    {
        return $this->hasMany(SiteServerDeployment::class);
    }
}
