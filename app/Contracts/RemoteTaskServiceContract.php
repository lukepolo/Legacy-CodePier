<?php

namespace App\Contracts;

use App\Models\Server\Server;
use App\Services\RemoteTaskService;

interface RemoteTaskServiceContract
{
    public function run(array $command, bool $read = false, $expectedFailure = false);

}
