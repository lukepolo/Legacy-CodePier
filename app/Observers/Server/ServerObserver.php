<?php

namespace App\Observers\Server;

use App\Models\Server\Server;
use Carbon\Carbon;

class ServerObserver
{
    /**
     * @param Server $server
     */
    public function creating(Server $server)
    {
        $server->generateSudoPassword();
        $server->generateDatabasePassword();
    }

    /**
     * @param Server $server
     */
    public function updating(Server $server)
    {
        if ($server->isDirty('stats')) {
            $stats = $server->stats;
            $stats['stats_updated_at'] = Carbon::now()->toIso8601String();

            $server->stats = $stats;
        }
    }
}
