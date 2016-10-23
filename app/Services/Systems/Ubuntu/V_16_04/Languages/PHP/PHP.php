<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\PHP;

use App\Models\ServerCronJob;
use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\SystemService;

/**
 * Class Ubuntu16_04.
 */
class PHP
{
    use ServiceConstructorTrait;

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

    public function installPHP7()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get -y install zip unzip');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y php php-pgsql php-sqlite3 php-gd php-apcu php-curl php-mcrypt php-imap php-mysql php-memcached php-readline php-mbstring php-xml php-zip php-intl php-bcmath php-soap');

        $this->remoteTaskService->run('sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php/7.0/cli/php.ini');
        $this->remoteTaskService->run('sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php/7.0/cli/php.ini');

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service php7.0-fpm restart');
    }

    public function installPhpFpm()
    {
        // https://www.howtoforge.com/tutorial/apache-with-php-fpm-on-ubuntu-16-04/

        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y php-fpm');

        $this->remoteTaskService->run('sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php/7.0/fpm/php.ini');
        $this->remoteTaskService->run('sed -i "s/upload_max_filesize = .*/upload_max_filesize = 100M/" /etc/php/7.0/fpm/php.ini');
        $this->remoteTaskService->run('sed -i "s/post_max_size = .*/post_max_size = 100M/" /etc/php/7.0/fpm/php.ini');
        $this->remoteTaskService->run('sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php/7.0/fpm/php.ini');

        $this->remoteTaskService->run('sed -i "s/user = www-data/user = codepier/" /etc/php/7.0/fpm/pool.d/www.conf');
        $this->remoteTaskService->run('sed -i "s/group = www-data/group = codepier/" /etc/php/7.0/fpm/pool.d/www.conf');

        $this->remoteTaskService->run('sed -i "s/listen\.owner.*/listen.owner = codepier/" /etc/php/7.0/fpm/pool.d/www.conf');
        $this->remoteTaskService->run('sed -i "s/listen\.group.*/listen.group = codepier/" /etc/php/7.0/fpm/pool.d/www.conf');
        $this->remoteTaskService->run('sed -i "s/;listen\.mode.*/listen.mode = 0666/" /etc/php/7.0/fpm/pool.d/www.conf');
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
}
