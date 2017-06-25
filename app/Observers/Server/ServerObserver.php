<?php

namespace App\Observers\Server;

use Carbon\Carbon;
use App\Models\Server\Server;

class ServerObserver
{
    /**
     * @param Server $server
     */
    public function creating(Server $server)
    {
        $server->sudo_password = str_random(32);
        $server->database_password = str_random(32);
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
