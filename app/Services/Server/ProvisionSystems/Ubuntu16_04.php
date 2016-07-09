<?php

namespace App\Services\Server\ProvisionSystems;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Models\Server;

/**
 * Class Ubuntu16_04
 * @package App\Services\Server\ProvisionRepositories
 */
class Ubuntu16_04
{
    private $remoteTaskService;

    /**
     * ProvisionService constructor.
     * @param RemoteTaskService $remoteTaskService
     * @param Server $server
     */
    public function __construct(RemoteTaskService $remoteTaskService, Server $server)
    {
        $this->remoteTaskService = $remoteTaskService;
        $this->remoteTaskService->ssh($server->ip);
    }

    public function updateSystem()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get update');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get -y upgrade');
    }

    public function setTimezoneToUTC()
    {
        $this->remoteTaskService->run('ln -sf /usr/share/zoneinfo/UTC /etc/localtime');
    }

    public function addCodePierUser()
    {
        $this->remoteTaskService->run('adduser --disabled-password --gecos "" codepier');
        $this->remoteTaskService->run('echo \'codepier:mypass\' | chpasswd');
        $this->remoteTaskService->run('adduser codepier sudo');
        $this->remoteTaskService->run('usermod -a -G www-data codepier');

        $this->remoteTaskService->run('mkdir /home/codepier/.ssh && cp -a ~/.ssh/authorized_keys /home/codepier/.ssh/authorized_keys');
        $this->remoteTaskService->run('chmod 700 /home/codepier/.ssh && chmod 600 /home/codepier/.ssh/authorized_keys');

        $this->remoteTaskService->run('ssh-keygen -t rsa -N "" -f /home/codepier/.ssh/id_rsa');
        $this->remoteTaskService->run('chown codepier:codepier /home/codepier/.ssh -R');
    }

    public function setLocaleToUTF8()
    {
        // Force Locale
        $this->remoteTaskService->run('echo "LC_ALL=en_US.UTF-8" >> /etc/default/locale');
        $this->remoteTaskService->run('locale-gen en_US.UTF-8');
    }

    public function installPHP()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get -y install zip unzip');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y php php-pgsql php-sqlite3 php-gd php-apcu php-curl php-mcrypt php-imap php-mysql php-memcached php-readline php-mbstring php-xml php-zip php-intl php-bcmath php-soap');

//        TODO - Not working!!!!!
//        $this->remoteTaskService->run('sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/7.0/cli/php.ini');
//        $this->remoteTaskService->run('sed -i "s/display_errors = .*/display_errors = On/" /etc/php/7.0/cli/php.ini');
//        $this->remoteTaskService->run('sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php/7.0/cli/php.ini');
//        $this->remoteTaskService->run('sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php/7.0/cli/php.ini');
    }

    public function installNginx()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y nginx');

        $this->remoteTaskService->run('rm /etc/nginx/sites-enabled/default');
        $this->remoteTaskService->run('rm /etc/nginx/sites-available/default');

        $this->remoteTaskService->run('sed -i "s/user www-data;/user codepier;/" /etc/nginx/nginx.conf');
        $this->remoteTaskService->run('sed -i "s/# server_names_hash_bucket_size.*/server_names_hash_bucket_size 64;/" /etc/nginx/nginx.conf');

        $this->remoteTaskService->run('service nginx restart');
        $this->remoteTaskService->run('service php7.0-fpm restart');
    }

    public function installPhpFpm()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y php-fpm');

        $this->remoteTaskService->run('sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/7.0/fpm/php.ini');
        $this->remoteTaskService->run('sed -i "s/display_errors = .*/display_errors = On/" /etc/php/7.0/fpm/php.ini');
        $this->remoteTaskService->run('sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/7.0/fpm/php.ini');
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

    public function installSupervisor()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y supervisor');
    }

    public function installGit()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y git');
    }

    public function installRedis()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y redis-server');

    }

    public function installMemcached()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y memcached');
    }

    public function installBeanstalk()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y beanstalkd');
        $this->remoteTaskService->run('sed -i "s/#START=yes/START=yes/" /etc/default/beanstalkd');
        $this->remoteTaskService->run('/etc/init.d/beanstalkd start');
    }

    public function installComposer()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y composer');
    }

    public function installLaravelInstaller()
    {
        $this->remoteTaskService->run('sudo su codepier;  composer global require "laravel/installer=~1.1"');
    }

    public function installEnvoy()
    {
        $this->remoteTaskService->run('sudo su codepier;  composer global require "laravel/envoy=~1.0"');
    }

    public function installNodeJs()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y nodejs npm');
    }

    public function installGulp()
    {
        $this->remoteTaskService->run('npm install -g gulp');
    }

    public function installBower()
    {
        $this->remoteTaskService->run('npm install -g bower');
    }

    public function installMySQL()
    {
        $this->remoteTaskService->run('debconf-set-selections <<< \'mysql-server mysql-server/root_password password secret\'');
        $this->remoteTaskService->run('debconf-set-selections <<< \'mysql-server mysql-server/root_password_again password secret\'');

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server');

        $this->remoteTaskService->run('sed -i \'/^bind-address/s/bind-address.*=.*/bind-address = 0.0.0.0/\' /etc/mysql/mysql.conf.d/mysqld.cnf');

        $this->remoteTaskService->run('mysql --user="root" --password="secret" -e "GRANT ALL ON *.* TO root@\'%\' IDENTIFIED BY \'secret\' WITH GRANT OPTION;"');
        $this->remoteTaskService->run('service mysql restart');

        $this->remoteTaskService->run('mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=secret mysql');
    }

    public function installMariaDB()
    {

    }

    public function installCertBot()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y letsencrypt');
    }

    public function createSwap()
    {
        $this->remoteTaskService->run('fallocate -l 1G /swapfile');
        $this->remoteTaskService->run('chmod 600 /swapfile');
        $this->remoteTaskService->run('mkswap /swapfile');
        $this->remoteTaskService->run('swapon /swapfile');
        $this->remoteTaskService->run('cp /etc/fstab /etc/fstab.bak');
        $this->remoteTaskService->run('echo \'/swapfile none swap sw 0 0\' | tee -a /etc/fstab');
        $this->remoteTaskService->run('echo "vm.swappiness=10" >> /etc/sysctl.conf');
        $this->remoteTaskService->run('echo "vm.vfs_cache_pressure=50" >> /etc/sysctl.conf');
    }

    public function errors()
    {
        $this->remoteTaskService->getErrors();
    }
}