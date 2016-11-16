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

    protected $appends = [
        'type',
        'class',
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

    public function getTypeAttribute()
    {
        return get_class($this);
    }

    public function getClassAttribute()
    {
        //        event-status-neutral
        //        event-status-success
        //        event-status-error
        //        event-status-warning


//        $statuses = $this->serverDeployments->pluck('status');
//
//        dd($statuses);


        return 'event-status-success';
    }
}
