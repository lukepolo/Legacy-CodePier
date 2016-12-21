<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\PHP;

use App\Services\RemoteTaskService;
use App\Models\Server\ServerCronJob;
use App\Services\Systems\SystemService;
use App\Services\Systems\ServiceConstructorTrait;

class PHP
{
    use ServiceConstructorTrait;

    /** @var RemoteTaskService $remoteTaskService */
    private $remoteTaskService;

    public static $required = [
        'PHP7',
        'PhpFpm',
    ];

    public static $files = [
        'PHP7' => [
            '/etc/php/7.0/fpm/php.ini',
            '/etc/php/7.0/cli/php.ini',
            '/etc/php/7.0/fpm/php-fpm.conf',
        ],
    ];

    public static $cronJobs = [
        'Laravel Scheduler' => '* * * * * php {site_path} schedule:run >> /dev/null 2>&1',
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
        'WorkerService' => [
            'Beanstalk',
            'Supervisor',
        ],
        'DatabaseService' => [
            'Redis',
            'MariaDB',
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
     * @options 7.0, 7.1
     * @multiple false
     */
    public function installPHP($version = '7.0')
    {
        $this->connectToServer();

        switch ($version) {
            case '7.0':
                $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get -y install zip unzip');
                $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y php7.0-cli php7.0-dev php7.0-pgsql php7.0-sqlite3 php7.0-gd php7.0-curl php7.0-memcached php7.0-imap php7.0-mysql php7.0-mbstring php7.0-xml php7.0-zip php7.0-bcmath php7.0-soap php7.0-intl php7.0-readline php-xdebug');
                $this->remoteTaskService->updateText('/etc/php/7.0/cli/php.ini', 'memory_limit =', 'memory_limit = 512M');
                $this->remoteTaskService->updateText('/etc/php/7.0/cli/php.ini', ';date.timezone.', 'date.timezone = UTC');

                $this->addToServiceRestartGroup(SystemService::DEPLOYMENT_SERVICE_GROUP, 'service php7.0-fpm restart');
                break;
            case '7.1':
                $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive add-apt-repository ppa:ondrej/php');
                $this->remoteTaskService->run('apt-get update');
                $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y php7.1-cli php7.1-dev php7.1-pgsql php7.1-sqlite3 php7.1-gd php7.1-curl php7.1-memcached php7.1-imap php7.1-mysql php7.1-mbstring php7.1-xml php7.1-zip php7.1-bcmath php7.1-soap php7.1-intl php7.1-readline php-xdebug');

                $this->remoteTaskService->updateText('/etc/php/7.1/cli/php.ini', 'memory_limit =', 'memory_limit = 512M');
                $this->remoteTaskService->updateText('/etc/php/7.1/cli/php.ini', ';date.timezone.', 'date.timezone = UTC');

                $this->addToServiceRestartGroup(SystemService::DEPLOYMENT_SERVICE_GROUP, 'service php7.1-fpm restart');
                break;
        }
    }

    /**
     * @description PHP-FPM is required when using Nnginx
     */
    public function installPhpFpm()
    {
        // TODO - we need to get the PHP version from the system
        $phpVersion = '7.0';
        // https://www.howtoforge.com/tutorial/apache-with-php-fpm-on-ubuntu-16-04/

        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y php-fpm');

        switch ($phpVersion) {
            case '7.1':
                $this->remoteTaskService->updateText('/etc/php/7.0/fpm/php.ini', 'memory_limit =', 'memory_limit = 512M');
                $this->remoteTaskService->updateText('/etc/php/7.0/fpm/php.ini', 'upload_max_filesize =', 'memory_limit = 250M');
                $this->remoteTaskService->updateText('/etc/php/7.0/fpm/php.ini', 'post_max_size =', 'post_max_size = 250M');
                $this->remoteTaskService->updateText('/etc/php/7.0/fpm/php.ini', ';date.timezone', 'date.timezone = UTC');

                $this->remoteTaskService->updateText('/etc/php/7.0/fpm/pool.d/www.conf', 'user = www-data', 'user = codepier');
                $this->remoteTaskService->updateText('/etc/php/7.0/fpm/pool.d/www.conf', 'group = www-data', 'group = codepier');

                $this->remoteTaskService->updateText('/etc/php/7.0/fpm/pool.d/www.conf', 'listen.owner', 'listen.owner = codepier');
                $this->remoteTaskService->updateText('/etc/php/7.0/fpm/pool.d/www.conf', 'listen.group', 'listen.group = codepier');
                $this->remoteTaskService->updateText('/etc/php/7.0/fpm/pool.d/www.conf', 'listen.mode', 'listen.mode = 0666');
                break;
        }
    }

    /**
     * @description Composer Dependency Manager for PHP
     */
    public function installComposer()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y composer');

        $cronJob = '* 1 * * * /usr/local/bin/composer self-update';

        $this->remoteTaskService->run('crontab -l | (grep '.$cronJob.') || ((crontab -l; echo "'.$cronJob.' >/dev/null 2>&1") | crontab)');

        ServerCronJob::create([
            'server_id' => $this->server->id,
            'job'       => $cronJob,
            'user'      => 'root',
        ]);
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
        $this->remoteTaskService->run('apt-get install blackfire-agent blackfire-php');

        $this->remoteTaskService->run("blackfire-agent --server-id=$serverID --server-token=$serverToken -d > /etc/blackfire/agent");

        $this->remoteTaskService->run('service blackfire-agent restart');
        $this->remoteTaskService->run('service php7.0-fpm restart');
    }

    private function getNginxConfig()
    {
    }
}
