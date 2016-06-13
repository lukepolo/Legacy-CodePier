<?php

namespace App\Services\Server\Site;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\Site\SiteServiceContract;
use App\Models\Server;

/**
 * Class SiteService
 * @package App\Services
 */
class SiteService implements SiteServiceContract
{
    protected $remoteTaskService;

    /**
     * SiteService constructor.
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     */
    public function __construct(RemoteTaskService $remoteTaskService)
    {
        $this->remoteTaskService = $remoteTaskService;
    }

    public function create(Server $server, $domain = 'default')
    {
        return $this->remoteTaskService->run(
            $server->ip,
            'root',
            'create_site', [
                'domain' => $domain
            ]
        );
    }

    public function deploy(Server $server)
    {
        return $this->remoteTaskService->run(
            $server->ip,
            'codepier',
            'deploy', [
                'branch' => 'master',
                'path' => '/home/codepier/default'
            ]
        );
    }
}