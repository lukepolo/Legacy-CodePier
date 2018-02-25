<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\PHP;

use App\Models\CronJob;
use App\Models\Site\Site;
use App\Services\RemoteTaskService;
use App\Services\Systems\SystemService;
use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\Ubuntu\V_16_04\WebService;

class PHP
{
    use ServiceConstructorTrait;

    /** @var RemoteTaskService $remoteTaskService */
    private $remoteTaskService;

    public static $files = [
        'PHP' => [
            '/etc/php/{version}/fpm/php.ini',
            '/etc/php/{version}/cli/php.ini',
            '/etc/php/{version}/fpm/php-fpm.conf',
        ],
    ];

    public $suggestedFeatures = [
        'OsService' => [
            'Swap',
        ],
        'WebService' => [
            'Nginx',
            'CertBot',
        ],
        'NodeService' => [
            'Yarn',
            'NodeJs',
        ],
        'FirewallService' => [
            'Fail2ban',
        ],
        'WorkerService' => [
            'Beanstalk',
            'Supervisor',
        ],
        'DatabaseService' => [
            'Redis',
            'MySQL',
        ],
        'Languages\PHP\PHP' => [
            'PHP',
            'PhpFpm',
            'Composer',
        ],
        'MonitoringService' => [
            'DiskMonitoringScript',
            'LoadMonitoringScript',
            'ServerMemoryMonitoringScript',
        ],
        'RepositoryService' => [
            'Git',
        ],
    ];

    /**
     * @description PHP
     *
     * @options 7.0, 7.1, 7.2
     * @multiple false
     */
    public function installPHP($version = '7.2')
    {
        $this->connectToServer();

        switch ($version) {
            case '7.0':
                $installVersion = '';
                break;
            default:
                $installVersion = $version;
                // https://github.com/oerdnj/deb.sury.org/issues/56#issuecomment-191748654
                $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php');
                $this->remoteTaskService->run('apt-get update');
                break;
        }

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y php'.$installVersion.'-cli php'.$installVersion.'-dev php'.$installVersion.'-pgsql php'.$installVersion.'-sqlite3 php'.$installVersion.'-gd php'.$installVersion.'-curl php'.$installVersion.'-memcached php'.$installVersion.'-imap php'.$installVersion.'-mysql php'.$installVersion.'-mbstring php'.$installVersion.'-xml php'.$installVersion.'-zip php'.$installVersion.'-bcmath php'.$installVersion.'-soap php'.$installVersion.'-intl php'.$installVersion.'-readline php'.$installVersion.'-mongodb '.$installVersion.'-ldap');

        $this->remoteTaskService->updateText("/etc/php/$version/cli/php.ini", 'date.timezone', 'date.timezone = UTC');
    }

    /**
     * @description PHP-FPM is required when using Nginx
     */
    public function installPhpFpm()
    {
        $this->connectToServer();

        $tempVersion = $version = $this->getPhpVersion();

        if ('7.0' == $version) {
            $tempVersion = '';
        }

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y php'.$tempVersion.'-fpm');

        $this->remoteTaskService->updateText("/etc/php/$version/fpm/php.ini", 'memory_limit =', 'memory_limit = 512M');
        $this->remoteTaskService->updateText("/etc/php/$version/fpm/php.ini", 'upload_max_filesize =', 'memory_limit = 250M');
        $this->remoteTaskService->updateText("/etc/php/$version/fpm/php.ini", 'post_max_size =', 'post_max_size = 250M');
        $this->remoteTaskService->updateText("/etc/php/$version/fpm/php.ini", 'date.timezone', 'date.timezone = UTC');

        $this->remoteTaskService->updateText('/etc/php/'.$version.'/fpm/pool.d/www.conf', 'user = www-data', 'user = codepier');
        $this->remoteTaskService->updateText('/etc/php/'.$version.'/fpm/pool.d/www.conf', 'group = www-data', 'group = codepier');

        $this->remoteTaskService->updateText('/etc/php/'.$version.'/fpm/pool.d/www.conf', 'listen.owner', 'listen.owner = codepier');
        $this->remoteTaskService->updateText('/etc/php/'.$version.'/fpm/pool.d/www.conf', 'listen.group', 'listen.group = codepier');
        $this->remoteTaskService->updateText('/etc/php/'.$version.'/fpm/pool.d/www.conf', 'listen.mode', 'listen.mode = 0666');

        $this->addToServiceRestartGroup(SystemService::DEPLOYMENT_SERVICE_GROUP, 'service php'.$version.'-fpm reload');
        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service php'.$version.'-fpm reload');
    }

    /**
     * @description Composer Dependency Manager for PHP
     */
    public function installComposer()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('curl -sS https://getcomposer.org/installer -o composer-setup.php && php composer-setup.php --install-dir=/usr/local/bin --filename=composer && rm composer-setup.php');

        $cronJob = '* 1 * * * /usr/local/bin/composer self-update';

        $this->remoteTaskService->run('crontab -l | (grep '.$cronJob.') || ((crontab -l; echo "'.$cronJob.' > /dev/null 2>&1") | crontab)');

        $this->server->cronJobs()->save(
            CronJob::create([
                'job'       => $cronJob,
                'user'      => 'root',
            ])
        );
    }

    /**
     * @description Blackfire empowers all developers and IT/Ops to continuously verify and improve their app’s performance, throughout its lifecycle, by getting the right information at the right moment. (https://blackfire.io/)
     */
    public function installBlackFire($serverID, $serverToken)
    {
        $this->connectToServer();

        $this->remoteTaskService->run('wget -O - https://packagecloud.io/gpg.key | apt-key add -');
        $this->remoteTaskService->run('echo "deb http://packages.blackfire.io/debian any main" | tee /etc/apt/sources.list.d/blackfire.list');
        $this->remoteTaskService->run('apt-get update');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install blackfire-agent blackfire-php');

        $this->remoteTaskService->run("blackfire-agent --server-id=$serverID --server-token=$serverToken -d > /etc/blackfire/agent");

        $this->remoteTaskService->run('service blackfire-agent restart');
        $this->remoteTaskService->run('service php7.0-fpm restart');
    }

    public function getNginxConfig(Site $site)
    {
        $frameworkConfig = '';
        if (! empty($site->framework)) {
            $frameworkConfig = $this->getFrameworkService($site)->getNginxConfig($site, $this->server->getLanguages()['PHP']['version']);
        }

        return '
    index index.html index.htm index.php;
    
    '.$frameworkConfig.'

  
    location ~ \.php$ {
        return 404;
    }
';
    }

    private function getPhpVersion()
    {
        return $this->server->getLanguages()['PHP']['version'];
    }

    private function getFrameworkService(Site $site)
    {
        return create_system_service('Languages\\'.$site->getFrameworkClass(), $this->server);
    }
}
