<?php

namespace App\Services\Server;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\ProvisionServiceContract;
use App\Models\Server;
use App\Services\Server\ProvisionSystems\Ubuntu16_04;

/**
 * Class ProvisionService
 * @package App\Services
 */
class ProvisionService implements ProvisionServiceContract
{
    private $remoteTaskService;

    protected $provisionSystems = [
        'ubuntu 16.04' => Ubuntu16_04::class
    ];

    /**
     * ProvisionService constructor.
     * @param RemoteTaskService $remoteTaskService
     */
    public function __construct(RemoteTaskService $remoteTaskService)
    {
        $this->remoteTaskService = $remoteTaskService;
    }

    /**
     * Provisions a server based on its operating system
     * @param Server $server
     */
    public function provision(Server $server)
    {
        $provisionSystem = $this->getProvisionRepository($server);

        $provisionSystem->updateSystem();
        $provisionSystem->setTimezoneToUTC();
        $provisionSystem->addCodePierUser();
        $provisionSystem->setLocaleToUTF8();
        $provisionSystem->createSwap();

        $provisionSystem->installGit();

        $provisionSystem->installPHP();
        $provisionSystem->installPhpFpm();
        $provisionSystem->installComposer();

        $provisionSystem->installNginx();

        $provisionSystem->installRedis();
        $provisionSystem->installMemcached();

        $provisionSystem->installSupervisor();
        $provisionSystem->installBeanstalk();

        $provisionSystem->installMySQL();
        $provisionSystem->installMariaDB();

        $provisionSystem->installNodeJs();
        $provisionSystem->installGulp();
        $provisionSystem->installBower();

        // TODO - having issues with the laravel installer and envoy installer
//        $provisionSystem->installLaravelInstaller();
//        $provisionSystem->installEnvoy();

        return $provisionSystem->errors();
    }

    /**
     * @param Server $server
     * @return mixed
     */
    private function getProvisionRepository(Server $server)
    {
        $operatingSystem = 'ubuntu 16.04';

        return new $this->provisionSystems[$operatingSystem]($this->remoteTaskService, $server);
    }
}