<?php

namespace App\Contracts;

use App\Models\Server\Server;
use App\Services\RemoteTaskService;

interface RemoteTaskServiceContract
{
    public function run($commands, bool $read = false, $expectedFailure = false);

    public function connectTo($server, $user = 'root'): void;

}
