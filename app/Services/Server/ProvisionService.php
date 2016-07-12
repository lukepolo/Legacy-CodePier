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
use App\Services\Server\ProvisionSystems\Ubuntu16_04;

/**
 * Class ProvisionService
 * @package App\Services
 */
class ProvisionService implements ProvisionServiceContract
{
    protected $totalActions;
    protected $doneActions = 0;
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

        $this->totalActions = count(get_class_methods($provisionSystem)) - 1;

        $provisionSystem->updateSystem();
        event(new UpdatedSystem($server, $this->getDonePercentage()));

        $provisionSystem->setTimezoneToUTC();
        event(new TimeZoneSetToUCT($server, $this->getDonePercentage()));

        $provisionSystem->addCodePierUser($sudoPassword);
        event(new AddedCodePierUser($server, $this->getDonePercentage()));

        $provisionSystem->setLocaleToUTF8();
        event(new LocaleSetToUTF8($server, $this->getDonePercentage()));

        $provisionSystem->createSwap();
        event(new SwapCreated($server, $this->getDonePercentage()));

        $provisionSystem->installGit();
        event(new GitInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installPHP();
        event(new PHPInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installPhpFpm();
        event(new PHPFpmInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installComposer();
        event(new ComposerInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installNginx();
        event(new NginxInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installRedis();
        event(new RedisInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installMemcached();
        event(new MemcachedInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installSupervisor();
        event(new SupervisorInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installBeanstalk();
        event(new BeanstalkInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installMySQL($databasePassword);
        event(new MySQLInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installMariaDB($databasePassword);
        event(new MariaDBInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installNodeJs();
        event(new NodeJsInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installGulp();
        event(new GulpInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installBower();
        event(new BowerInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installCertBot();
        event(new CertBotInstalled($server, $this->getDonePercentage()));

        $provisionSystem->installFirewallRules();
        event(new FirewallSetup($server, $this->getDonePercentage()));

        // TODO - having issues with the laravel installer and envoy installer
//        $provisionSystem->installLaravelInstaller();
//        $provisionSystem->installEnvoy();

        return $provisionSystem->errors();
    }

    private function getDonePercentage()
    {
       return floor((++$this->doneActions / $this->totalActions) * 100);
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