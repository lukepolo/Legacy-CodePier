<?php

namespace App\Services\Server;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\ProvisionServiceContract;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Models\Server;
use App\Services\Server\ProvisionSystems\Ubuntu16_04;

/**
 * Class ProvisionService
 * @package App\Services
 */
class ProvisionService implements ProvisionServiceContract
{
    protected $server;
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
        $this->server = $server;

        $provisionSystem = $this->getProvisionRepository($server);

        $this->totalActions = count(get_class_methods($provisionSystem)) - 4;

        $this->updateProgress('Updating system');
        $provisionSystem->updateSystem();

        $this->updateProgress('Settings Timezone to UTC');
        $provisionSystem->setTimezoneToUTC();

        $this->updateProgress('Settings Locale to UTF8');
        $provisionSystem->setLocaleToUTF8();

        $this->updateProgress('Creating Swap');
        $provisionSystem->createSwap();

        $this->updateProgress('Adding CodePier User');
        $provisionSystem->addCodePierUser($sudoPassword);

        $this->updateProgress('Installing GIT');
        $provisionSystem->installGit($server);

        $this->updateProgress('Installing PHP');
        $provisionSystem->installPHP();

        $this->updateProgress('Installing PHP-FPM');
        $provisionSystem->installPhpFpm();

        $this->updateProgress('Installing Composer');
        $provisionSystem->installComposer();

        $this->updateProgress('Installing Nginx');
        $provisionSystem->installNginx();

        $this->updateProgress('Installing Redis');
        $provisionSystem->installRedis();

        $this->updateProgress('Installing Memcached');
        $provisionSystem->installMemcached();

        $this->updateProgress('Installing Supervisor');
        $provisionSystem->installSupervisor();

        $this->updateProgress('Installing Beanstalk');
        $provisionSystem->installBeanstalk();

        $database = isset($server->options['database']) ? $server->options['database'] : $server->name;

        if($server->hasFeature('mariaDB')) {
            $this->updateProgress('Installing MariaDB');
            $provisionSystem->installMariaDB($databasePassword, $database);
        } else {
            $this->updateProgress('Installing MySQL');
            $provisionSystem->installMySQL($databasePassword, $database);
        }

        $this->updateProgress('Installing NodeJS');
        $provisionSystem->installNodeJs();

        $this->updateProgress('Installing Gulp');
        $provisionSystem->installGulp();

        $this->updateProgress('Installing Bower');
        $provisionSystem->installBower();

        $this->updateProgress('Installing LetsEncrypt - Cert Bot');
        $provisionSystem->installCertBot();

        $this->updateProgress('Installing Basic Firewall Rules');
        $provisionSystem->installFirewallRules($server);

        // TODO - having issues with the laravel installer and envoy installer
//        $provisionSystem->installLaravelInstaller();
//        $provisionSystem->installEnvoy();


        $this->updateProgress('Installing disk monitor script');
        $provisionSystem->addDiskMonitoringScript($server);

        return $provisionSystem->errors();
    }

    public function provisionLoadBalancer(Server $server, $sudoPassword, $databasePassword)
    {
        $this->server = $server;

        $provisionSystem = $this->getProvisionRepository($server);

        $this->updateProgress('Updating system');
        $provisionSystem->updateSystem();

        $this->updateProgress('Settings Timezone to UTC');
        $provisionSystem->setTimezoneToUTC();

        $this->updateProgress('Settings Locale to UTF8');
        $provisionSystem->setLocaleToUTF8();

        $this->updateProgress('Creating Swap');
        $provisionSystem->createSwap();

        $this->updateProgress('Adding CodePier User');
        $provisionSystem->addCodePierUser($sudoPassword);

        $this->updateProgress('Installing Nginx');
        $provisionSystem->installNginx();

        $this->updateProgress('Installing LetsEncrypt - Cert Bot');
        $provisionSystem->installCertBot();

        $this->updateProgress('Installing Basic Firewall Rules');
        $provisionSystem->installFirewallRules($server);

    }

    private function updateProgress($status)
    {
        $progress = floor((++$this->doneActions / $this->totalActions) * 100);

        event(new ServerProvisionStatusChanged($this->server, $status, $progress));
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

    public function installDiskMonitorScript(Server $server)
    {
        $provisionSystem = $this->getProvisionRepository($server);

        event(new ServerProvisionStatusChanged($server, 'Provisioned', 100));
        $provisionSystem->addDiskMonitoringScript($server);
    }
}