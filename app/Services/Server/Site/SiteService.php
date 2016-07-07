<?php

namespace App\Services\Server\Site;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\Site\SiteServiceContract;
use App\Models\Server;
use App\Models\Site;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SFTP;
use phpseclib\Net\SSH2;

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

    /**
     * Creates a site on the server
     *
     * @param Server $server
     * @param string $domain
     * @return bool
     */
    public function create(Server $server, $domain = 'default')
    {
        if ($this->remoteTaskService->run(
            $server->ip,
            'root',
            'create_site', [
            'domain' => $domain
        ])
        ) {
            Site::create([
                'domain' => $domain,
                'server_id' => $server->id,
                'wildcard_domain' => false,
                'zerotime_deployment' => false,
                'user_id' => $server->user_id,
                'path' => '/home/codepier/' . $domain,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Deploys a site on the server
     *
     * @param Server $server
     * @param Site $site
     * @param bool $zeroDownTime
     *
     * @return bool
     */
    public function deploy(Server $server, Site $site, $zeroDownTime = true)
    {
        return $this->remoteTaskService->run(
            $server->ip,
            'codepier',
            'deploy', [
                'repository' => $site->repository,
                'branch' => $site->branch,
                'path' => $site->path
            ]
        );
    }

    public function getFile(Server $server, $filePath)
    {
        $key = new RSA();
        $key->setPassword(env('SSH_KEY_PASSWORD'));
        $key->loadKey(file_get_contents('/home/vagrant/.ssh/id_rsa'));

        $ssh = new SFTP($server->ip);

        if (!$ssh->login('root', $key)) {
            exit('Login Failed');
        }

        if($contents = $ssh->get($filePath)) {
            return $contents;
        }
    }
}