<?php

namespace App\Services\Server;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\ProvisionServiceContract;
use App\Events\Server\AddedCodePierUser;
use App\Events\Server\BeanstalkInstalled;
use App\Events\Server\BowerInstalled;
use App\Events\Server\CertBotInstalled;
use App\Events\Server\ComposerInstalled;
use App\Events\Server\GitInstalled;
use App\Events\Server\GulpInstalled;
use App\Events\Server\LocaleSetToUTF8;
use App\Events\Server\MariaDBInstalled;
use App\Events\Server\MemcachedInstalled;
use App\Events\Server\MySQLInstalled;
use App\Events\Server\NginxInstalled;
use App\Events\Server\NodeJsInstalled;
use App\Events\Server\PHPFpmInstalled;
use App\Events\Server\PHPInstalled;
use App\Events\Server\RedisInstalled;
use App\Events\Server\SupervisorInstalled;
use App\Events\Server\SwapCreated;
use App\Events\Server\TimeZoneSetToUCT;
use App\Events\Server\UpdatedSystem;
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
        event(new UpdatedSystem($server));

        $provisionSystem->setTimezoneToUTC();
        event(new TimeZoneSetToUCT($server));

        $provisionSystem->addCodePierUser();
        event(new AddedCodePierUser($server));

        $provisionSystem->setLocaleToUTF8();
        event(new LocaleSetToUTF8($server));

        $provisionSystem->createSwap();
        event(new SwapCreated($server));

        $provisionSystem->installGit();
        event(new GitInstalled($server));

        $provisionSystem->installPHP();
        event(new PHPInstalled($server));

        $provisionSystem->installPhpFpm();
        event(new PHPFpmInstalled($server));

        $provisionSystem->installComposer();
        event(new ComposerInstalled($server));

        $provisionSystem->installNginx();
        event(new NginxInstalled($server));

        $provisionSystem->installRedis();
        event(new RedisInstalled($server));

        $provisionSystem->installMemcached();
        event(new MemcachedInstalled($server));

        $provisionSystem->installSupervisor();
        event(new SupervisorInstalled($server));

        $provisionSystem->installBeanstalk();
        event(new BeanstalkInstalled($server));

        $provisionSystem->installMySQL();
        event(new MySQLInstalled($server));

        $provisionSystem->installMariaDB();
        event(new MariaDBInstalled($server));

        $provisionSystem->installNodeJs();
        event(new NodeJsInstalled($server));

        $provisionSystem->installGulp();
        event(new GulpInstalled($server));

        $provisionSystem->installBower();
        event(new BowerInstalled($server));

        $provisionSystem->installCertBot();
        event(new CertBotInstalled($server));
        
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