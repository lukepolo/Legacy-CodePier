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
        'status',
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

    public function getStatusAttribute()
    {
        $serverDeployments = $this->serverDeployments;

        $failed = $serverDeployments->sum('failed');
        $completed = $serverDeployments->sum('completed');

        if ($failed > 0) {
            return 'Failed';
        }

        if ($completed == $serverDeployments->count()) {
            return 'Completed';
        }
    }
}
