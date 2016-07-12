<?php

namespace App\Services\Server;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\ProvisionServiceContract;
use App\Events\Server\Provision\AddedCodePierUser;
use App\Events\Server\Provision\BeanstalkInstalled;
use App\Events\Server\Provision\BowerInstalled;
use App\Events\Server\Provision\CertBotInstalled;
use App\Events\Server\Provision\ComposerInstalled;
use App\Events\Server\Provision\FirewallSetup;
use App\Events\Server\Provision\GitInstalled;
use App\Events\Server\Provision\GulpInstalled;
use App\Events\Server\Provision\LocaleSetToUTF8;
use App\Events\Server\Provision\MariaDBInstalled;
use App\Events\Server\Provision\MemcachedInstalled;
use App\Events\Server\Provision\MySQLInstalled;
use App\Events\Server\Provision\NginxInstalled;
use App\Events\Server\Provision\NodeJsInstalled;
use App\Events\Server\Provision\PHPFpmInstalled;
use App\Events\Server\Provision\PHPInstalled;
use App\Events\Server\Provision\RedisInstalled;
use App\Events\Server\Provision\SupervisorInstalled;
use App\Events\Server\Provision\SwapCreated;
use App\Events\Server\Provision\TimeZoneSetToUCT;
use App\Events\Server\Provision\UpdatedSystem;
use App\Models\Server;
use App\Models\ServerFirewallRule;
use App\Services\Server\ProvisionSystems\Ubuntu16_04;

/**
 * Class ProvisionService
 * @package App\Services
 */
class ProvisionService implements ProvisionServiceContract
{
    protected $remoteTaskService;

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
     * @param $sudoPassword
     * @param $databasePassword
     * @return
     */
    public function provision(Server $server, $sudoPassword, $databasePassword)
    {
        $provisionSystem = $this->getProvisionRepository($server);

        $provisionSystem->updateSystem();
        event(new UpdatedSystem($server));

        $provisionSystem->setTimezoneToUTC();
        event(new TimeZoneSetToUCT($server));

        $provisionSystem->addCodePierUser($sudoPassword);
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

        $provisionSystem->installMySQL($databasePassword);
        event(new MySQLInstalled($server));

        $provisionSystem->installMariaDB($databasePassword);
        event(new MariaDBInstalled($server));

        $provisionSystem->installNodeJs();
        event(new NodeJsInstalled($server));

        $provisionSystem->installGulp();
        event(new GulpInstalled($server));

        $provisionSystem->installBower();
        event(new BowerInstalled($server));

        $provisionSystem->installCertBot();
        event(new CertBotInstalled($server));
        
        $provisionSystem->installFirewallRules();
        event(new FirewallSetup($server));

        // TODO - having issues with the laravel installer and envoy installer
        $provisionSystem->installLaravelInstaller();
        $provisionSystem->installEnvoy();

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