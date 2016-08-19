<?php

namespace App\Services\Server\ProvisionSystems;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Models\Server;

/**
 * Interface ProvisionSystemContract
 * @package App\Services\Server\ProvisionSystems
 */
interface ProvisionSystemContract
{
    /**
     * ProvisionService constructor.
     * @param RemoteTaskService $remoteTaskService
     * @param Server $server
     */
    public function __construct(RemoteTaskService $remoteTaskService, Server $server);

    /**
     * Updates the system package and distribution to the latest version
     * @return mixed
     */
    public function updateSystem();

    /**
     * Sets the servers timezone to UTC
     * @return mixed
     */
    public function setTimezoneToUTC();

    /**
     * Add the code pier user with a sudo password
     * @param Server $server
     * @param $sudoPassword
     * @return mixed
     */
    public function addCodePierUser(Server $server, $sudoPassword);

    /**
     * Set the servers locale to UTF8
     * @return mixed
     */
    public function setLocaleToUTF8();

    /**
     * Installs PHP and basic configurations
     * @return mixed
     */
    public function installPHP();

    /**
     * Installs Nginx and basic configurations
     * @return mixed
     */
    public function installNginx();

    /**
     * Installs PHP-FPM and basic configurations
     * @return mixed
     */
    public function installPhpFpm();

    /**
     * Installs Supervisor and basic configurations
     * @return mixed
     */
    public function installSupervisor();

    /**
     * Installs Git
     * @param Server $server
     * @return mixed
     */
    public function installGit(Server $server);

    /**
     * Installs Redis
     * @return mixed
     */
    public function installRedis();

    /**
     * Installs Memcached
     * @return mixed
     */
    public function installMemcached();

    /**
     * Installs PHP and basic configurations
     * @return mixed
     */
    public function installBeanstalk();

    /**
     * Installs Composer
     * @return mixed
     */
    public function installComposer();

    /**
     * Installs Laravel composer packages for easy site creation
     * @return mixed
     */
    public function installLaravelInstaller();

    /**
     * Installs Laravel Envoy
     * @return mixed
     */
    public function installEnvoy();

    /**
     * Installs NodeJS
     * @return mixed
     */
    public function installNodeJs();

    /**
     * Installs Gulp
     * @return mixed
     */
    public function installGulp();

    /**
     * Installs Bower
     * @return mixed
     */
    public function installBower();

    /**
     * Installs MySQL and basic configurations with the database password
     * @param $databasePassword
     * @param null $database
     * @return mixed
     */
    public function installMySQL($databasePassword, $database = null);

    /**
     * Installs MariaDB and basic configurations with the database password
     * @param $databasePassword
     * @param null $database
     * @return mixed
     */
    public function installMariaDB($databasePassword, $database = null);

    /**
     * Installs Lets Encrypt's CertBot script
     * @return mixed
     */
    public function installCertBot();

    /**
     * Creats a system swap file
     * @return mixed
     */
    public function createSwap();

    /**
     * Installs basic firewall rules
     * @param Server $server
     * @return mixed
     */
    public function installFirewallRules(Server $server);

    /**
     * Gets the errors of the provisioning system
     * @return mixed
     */
    public function errors();

}