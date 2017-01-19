<?php

namespace App\Traits\Buoys;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

trait BuoyTrait
{
    private $serverService;
    private $remoteTaskService;
    private $domainable = false;

    /**
     * ElasticsearchBuoy constructor.
     * @param \App\Services\Server\ServerService |ServerService $serverService
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     */
    public function __construct(ServerService $serverService, RemoteTaskService $remoteTaskService)
    {
        $this->serverService = $serverService;
        $this->remoteTaskService = $remoteTaskService;
    }
}
