<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\PHP;

use App\Services\Systems\ServiceConstructorTrait;

/**
 * Class Ubuntu16_04.
 */
class PHP
{
    use ServiceConstructorTrait;

    private $remoteTaskService;

    public static $required = [
        'installPHP7',
        'installPhpFpm'
    ];

    public function installPHP7()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get -y install zip unzip');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y php php-pgsql php-sqlite3 php-gd php-apcu php-curl php-mcrypt php-imap php-mysql php-memcached php-readline php-mbstring php-xml php-zip php-intl php-bcmath php-soap');

        $this->remoteTaskService->run('sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php/7.0/cli/php.ini');
        $this->remoteTaskService->run('sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php/7.0/cli/php.ini');
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
    }

    public function installBlackFire($serverID, $serverToken)
    {
        $this->connectToServer();

        $this->remoteTaskService->run('wget -O - https://packagecloud.io/gpg.key | apt-key add -');
        $this->remoteTaskService->run('echo "deb http://packages.blackfire.io/debian any main" | tee /etc/apt/sources.list.d/blackfire.list');
        $this->remoteTaskService->run('apt-get update');
        $this->remoteTaskService->run('apt-get install blackfire-agent blackfire-php');

        $this->remoteTaskService->run('echo "'.$serverID.'\n'.$serverToken.'\n" | blackfire-agent -register');

        $this->remoteTaskService->updateText('/etc/blackfire/agent', 'server-id', $serverID);
        $this->remoteTaskService->updateText('/etc/blackfire/agent', 'server-token', $serverToken);

        $this->remoteTaskService->run('/etc/init.d/blackfire-agent restart');
    }
}
