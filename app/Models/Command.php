<?php

namespace App\Models;

use App\Models\Site\Site;
use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    protected $guarded = ['id'];

    protected $appends = [
        'status',
        'event_type',
    ];

    public function getEventTypeAttribute()
    {
        return get_class($this);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function serverCommands()
    {
        return $this->hasMany(ServerCommand::class);
    }

    public function getStatusAttribute()
    {
        $serverCommands = $this->serverCommands;

        $failed = $serverCommands->sum('failed');
        $started = $serverCommands->sum('started');
        $completed = $serverCommands->sum('completed');

        if ($failed > 0) {
            return 'Failed';
        }

        $totalServerDeployments = $serverCommands->count();

        if ($completed == $totalServerDeployments) {
            return 'Completed';
        }

        if ($started > 0) {
            return 'Running';
        }
    }
}
