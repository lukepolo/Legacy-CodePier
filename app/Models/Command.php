<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    use ConnectedToUser;

    public static $userModel = ['site.user', 'server.user'];

    protected $guarded = ['id'];

    protected $appends = [
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

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function serverCommands()
    {
        return $this->hasMany(ServerCommand::class);
    }

    public function updateStats()
    {
        $serverCommands = $this->serverCommands;

        $failed = $serverCommands->sum('failed');
        $started = $serverCommands->sum('started');
        $completed = $serverCommands->sum('completed');

        $status = 'Queued';

        if ($failed > 0) {
            $status = 'Failed';
        } else if ($completed == $serverCommands->count()) {
            $status = 'Completed';
        } else if ($started > 0) {
            $status = 'Running';
        }

        $this->update([
            'status' => $status
        ]);
    }

    /**
     * Get all of the owning commentable models.
     */
    public function commandable()
    {
        return $this->morphTo();
    }
}
