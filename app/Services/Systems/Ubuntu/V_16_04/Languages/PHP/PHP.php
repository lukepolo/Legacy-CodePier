<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\PHP;

use App\Models\Server\ServerCronJob;
use App\Services\RemoteTaskService;
use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\SystemService;

class PHP
{
    use ServiceConstructorTrait;

    /** @var RemoteTaskService $remoteTaskService */
    private $remoteTaskService;

    public static $required = [
        'installPHP7',
        'installPhpFpm',
    ];

    public static $files = [
        'installPHP7' => [
            '/etc/php/7.0/fpm/php.ini',
            '/etc/php/7.0/cli/php.ini',
            '/etc/php/7.0/fpm/php-fpm.conf',
        ],
    ];

    public static $cronJobs = [
        'Laravel Scheduler' => '* * * * * php {site_path} schedule:run >> /dev/null 2>&1',
    ];

    public function installPHP7()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get -y install zip unzip');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y php php-pgsql php-sqlite3 php-gd php-apcu php-curl php-mcrypt php-imap php-mysql php-memcached php-readline php-mbstring php-xml php-zip php-intl php-bcmath php-soap');

        $this->remoteTaskService->updateText('/etc/php/7.0/cli/php.ini', 'memory_limit =', 'memory_limit = 512M');
        $this->remoteTaskService->updateText('/etc/php/7.0/cli/php.ini', ';date.timezone.', 'date.timezone = UTC');

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service php7.0-fpm restart');
    }

    public function installPhpFpm()
    {
        // https://www.howtoforge.com/tutorial/apache-with-php-fpm-on-ubuntu-16-04/

        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y php-fpm');

        $this->remoteTaskService->updateText('/etc/php/7.0/fpm/php.ini', 'memory_limit =', 'memory_limit = 512M');
        $this->remoteTaskService->updateText('/etc/php/7.0/fpm/php.ini', 'upload_max_filesize =', 'memory_limit = 250M');
        $this->remoteTaskService->updateText('/etc/php/7.0/fpm/php.ini', 'post_max_size =', 'post_max_size = 250M');
        $this->remoteTaskService->updateText('/etc/php/7.0/fpm/php.ini', ';date.timezone', 'date.timezone = UTC');

        $this->remoteTaskService->updateText('/etc/php/7.0/fpm/pool.d/www.conf', 'user = www-data', 'user = codepier');
        $this->remoteTaskService->updateText('/etc/php/7.0/fpm/pool.d/www.conf', 'group = www-data', 'group = codepier');

        $this->remoteTaskService->updateText('/etc/php/7.0/fpm/pool.d/www.conf', 'listen.owner', 'listen.owner = codepier');
        $this->remoteTaskService->updateText('/etc/php/7.0/fpm/pool.d/www.conf', 'listen.group', 'listen.group = codepier');
        $this->remoteTaskService->updateText('/etc/php/7.0/fpm/pool.d/www.conf', 'listen.mode', 'listen.mode = 0666');
    }

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
