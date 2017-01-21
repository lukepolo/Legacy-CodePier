<?php

namespace App\Services;

use App\Models\Buoy;
use App\Models\Server\Server;
use App\Contracts\BuoyServiceContract;

class BuoyService implements BuoyServiceContract
{
    public function installBuoy(Server $server, Buoy $buoy)
    {
    }
}
