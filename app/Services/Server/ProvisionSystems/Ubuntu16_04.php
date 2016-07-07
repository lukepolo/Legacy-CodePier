<?php

namespace App\Services\Server\ProvisionSystems;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use Symfony\Component\Process\Process;

/**
 * Class Ubuntu16_04
 * @package App\Services\Server\ProvisionRepositories
 */
class Ubuntu16_04
{
    private $remoteTaskService;
    
    /** @var  Process $process */
    private $process;

    /**
     * ProvisionService constructor.
     * @param RemoteTaskService $remoteTaskService
     */
    public function __construct(RemoteTaskService $remoteTaskService, $ipAddress)
    {
        $this->remoteTaskService = $remoteTaskService;
        $this->process = $this->remoteTaskService->ssh($ipAddress);
    }

    public function updateSystem()
    {
        $this->process->setCommandLine('apt-get update');
        $this->process->run();
        
        dd($this->process->getOutput());
        
//        # Update Package List
//        apt-get update

//        # Update System Packages
//        apt-get -y upgrade
    }


    public function setTimezoneToUTC()
    {
//        ln -sf /usr/share/zoneinfo/UTC /etc/localtime
    }

    public function addCodePierUser()
    {
//        adduser --disabled-password --gecos "" codepier
//        echo 'codepier:mypass' | chpasswd
//        adduser codepier sudo
//        usermod -a -G www-data codepier
//
//        # Allow user to login as codepier
//        mkdir /home/codepier/.ssh && cp -a ~/.ssh/authorized_keys /home/codepier/.ssh/authorized_keys
//        chmod 700 /home/codepier/.ssh && chmod 600 /home/codepier/.ssh/authorized_keys
//
//        # Generate ssh key for codepier
//        ssh-keygen -t rsa -N "" -f /home/codepier/.ssh/id_rsa
//
//        chown codepier:codepier /home/codepier/.ssh -R
    }

    public function setLocaleToUTF8()
    {
//        # Force Locale
//        echo "LC_ALL=en_US.UTF-8" >> /etc/default/locale
//    locale-gen en_US.UTF-8
    }

    public function installPHP()
    {

//        apt-get update
//    apt-get install -y php7.0-cli php7.0-dev php-pgsql php-sqlite3 php-gd php-apcu php-curl php7.0-mcrypt php-imap php-mysql php-memcached php7.0-readline php-mbstring php-dom php-xml php7.0-zip php7.0-intl php7.0-bcmath php-soap composer
//
//    # Set Some PHP CLI Settings
//
//    sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/7.0/cli/php.ini
//    sed -i "s/display_errors = .*/display_errors = On/" /etc/php/7.0/cli/php.ini
//    sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php/7.0/cli/php.ini
//    sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php/7.0/cli/php.ini

    }

    public function installNginx()
    {
//        apt-get update
//        apt-get install -y --force-yes nginx php7.0-fpm php-fpm-cli
//
//        rm /etc/nginx/sites-enabled/default
//        rm /etc/nginx/sites-available/default
//
//        sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/7.0/fpm/php.ini
//        sed -i "s/display_errors = .*/display_errors = On/" /etc/php/7.0/fpm/php.ini
//        sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/7.0/fpm/php.ini
//        sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php/7.0/fpm/php.ini
//        sed -i "s/upload_max_filesize = .*/upload_max_filesize = 100M/" /etc/php/7.0/fpm/php.ini
//        sed -i "s/post_max_size = .*/post_max_size = 100M/" /etc/php/7.0/fpm/php.ini
//        sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php/7.0/fpm/php.ini
//
//        # Set The Nginx & PHP-FPM User
//        sed -i "s/user www-data;/user codepier;/" /etc/nginx/nginx.conf
//        sed -i "s/# server_names_hash_bucket_size.*/server_names_hash_bucket_size 64;/" /etc/nginx/nginx.conf
//
//        sed -i "s/user = www-data/user = codepier/" /etc/php/7.0/fpm/pool.d/www.conf
//        sed -i "s/group = www-data/group = codepier/" /etc/php/7.0/fpm/pool.d/www.conf
//
//        sed -i "s/listen\.owner.*/listen.owner = codepier/" /etc/php/7.0/fpm/pool.d/www.conf
//        sed -i "s/listen\.group.*/listen.group = codepier/" /etc/php/7.0/fpm/pool.d/www.conf
//        sed -i "s/;listen\.mode.*/listen.mode = 0666/" /etc/php/7.0/fpm/pool.d/www.conf
//
//        service nginx restart
//        service php7.0-fpm restart

    }
    
    public function installPhpFpm()
    {

    }
    
    public function installSupervisor()
    {
//        apt-get install -y zip unzip git supervisor redis-server memcached beanstalkd
//
//        # Configure Beanstalkd
//        sed -i "s/#START=yes/START=yes/" /etc/default/beanstalkd
//        /etc/init.d/beanstalkd start
    }

    public function installRedis()
    {

    }
    public function installMemcached()
    {

    }
    public function installBeanstalk()
    {

    }
    
    public function installComposer()
    {

    }
    public function installLaravelInstaller()
    {

    }
    public function installEnvoy()
    {

    }
   
    public function installNodeJs()
    {
//        apt-get update
//        apt-get install -y nodejs
//        /usr/bin/npm install -g gulp
//        /usr/bin/npm install -g bower
    }
    public function installGulp()
    {

    }
    public function installBower()
    {

    }

    public function installMySQL()
    {
//        export DEBIAN_FRONTEND="noninteractive"
//        debconf-set-selections <<< 'mysql-server mysql-server/root_password password secret'
//        debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password secret'
//
//        apt-get install -y mysql-server
//
//        # Configure MySQL Remote Access
//        sed -i '/^bind-address/s/bind-address.*=.*/bind-address = 0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf
//
//        mysql --user="root" --password="secret" -e "GRANT ALL ON *.* TO root@'%' IDENTIFIED BY 'secret' WITH GRANT OPTION;"
//        service mysql restart
//
//        # Add Timezone Support To MySQL
//        mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=secret mysql
    }

    public function installMariaDB()
    {

    }

    public function createSwap()
    {
//        fallocate -l 1G /swapfile
//        chmod 600 /swapfile
//        mkswap /swapfile
//        swapon /swapfile
//        cp /etc/fstab /etc/fstab.bak
//        echo '/swapfile none swap sw 0 0' | tee -a /etc/fstab
//        echo "vm.swappiness=10" >> /etc/sysctl.conf
//        echo "vm.vfs_cache_pressure=50" >> /etc/sysctl.conf
    }
}