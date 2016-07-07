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

    public function provision(Server $server)
    {
        $provisionSystem = $this->getProvisionRepository('ubuntu 16.04');

        $provisionSystem->updateSystem();
        $provisionSystem->setTimezoneToUTC();
        $provisionSystem->addCodePierUser();
        $provisionSystem->setLocaleToUTF8();
        $provisionSystem->createSwap();


        $provisionSystem->installPHP();
        $provisionSystem->installNginx();
        $provisionSystem->installPhppm();
        $provisionSystem->installRedis();
        $provisionSystem->installMemcached();
        $provisionSystem->installBeanstalk();
        $provisionSystem->installComposer();
        $provisionSystem->installLaravelInstaller();
        $provisionSystem->installEnvoy();
        $provisionSystem->installNodeJs();
        $provisionSystem->installGulp();
        $provisionSystem->installBower();
        $provisionSystem->installMySQL();
        $provisionSystem->installMariaDB();
    }
    
    private function getProvisionRepository(Server $server, $ipAddress)
    {
        $operatingSystem = 'ubuntu 16.04';
        return new $this->remoteTaskService[$operatingSystem]($this->remoteTaskService, $ipAddress);
    }
}