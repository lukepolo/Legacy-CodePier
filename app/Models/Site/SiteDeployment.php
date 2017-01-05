<?php

namespace App\Models\Site;

use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class SiteDeployment extends Model
{
    const FAILED = 'Failed';
    const RUNNING = 'Running';
    const COMPLETED = 'Completed';
    const QUEUED_FOR_DEPLOYMENT = 'Queued';

    use ConnectedToUser;

    public static $userModel = 'site';

    protected $guarded = ['id'];

    protected $casts = [
        'log' => 'array',
    ];

    protected $appends = [
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

    public function updateStatus()
    {
        $this->load('serverDeployments');
        $serverDeployments = $this->serverDeployments;

        $failed = $serverDeployments->sum('failed');
        $started = $serverDeployments->sum('started');
        $completed = $serverDeployments->sum('completed');

        $status = self::QUEUED_FOR_DEPLOYMENT;

        if ($failed > 0) {
            $status = self::FAILED;
        } elseif ($completed == $serverDeployments->count()) {
            $status = self::COMPLETED;
        } elseif ($started > 0) {
            $status = self::RUNNING;
        }

        $this->update([
            'status' => $status,
        ]);
    }

    public function delete()
    {
        $this->serverDeployments()->delete();

        return parent::delete();
    }
}
