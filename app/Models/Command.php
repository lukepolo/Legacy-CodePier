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

    protected $casts = [
        'log' => 'array',
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

    public function updateStatus()
    {
        $this->load('serverCommands');
        $serverCommands = $this->serverCommands;

        $failed = $serverCommands->sum('failed');
        $started = $serverCommands->sum('started');
        $completed = $serverCommands->sum('completed');

        $status = 'Queued';

        if ($failed > 0) {
            $status = 'Failed';
        } elseif ($completed == $serverCommands->count()) {
            $status = 'Completed';
        } elseif ($started > 0) {
            $status = 'Running';
        }

        $this->update([
            'status' => $status,
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
