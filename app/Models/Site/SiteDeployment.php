<?php

namespace App\Models\Site;

use App\Traits\FireEvents;
use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class SiteDeployment extends Model
{
    use FireEvents, ConnectedToUser;

    public static $userModel = 'site';

    protected $guarded = ['id'];

    protected $casts = [
        'log' => 'array',
    ];

    protected $appends = [
        'status',
        'event_type',
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

    public function getEventTypeAttribute()
    {
        return get_class($this);
    }

    public function getStatusAttribute()
    {
        $serverDeployments = $this->serverDeployments;

        $failed = $serverDeployments->sum('failed');
        $started = $serverDeployments->sum('started');
        $completed = $serverDeployments->sum('completed');

        if ($failed > 0) {
            return 'Failed';
        }

        $totalServerDeployments = $serverDeployments->count();

        if ($completed == $totalServerDeployments) {
            return 'Completed';
        }

        if ($started > 0) {
            return 'Running';
        }
    }

    public function delete()
    {
        $this->serverDeployments()->delete();

        return parent::delete();
    }
}
