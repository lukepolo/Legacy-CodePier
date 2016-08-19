<?php

namespace App\Services\Server\ProvisionSystems;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Models\Server;
use App\Models\ServerFirewallRule;
use App\Models\Site;

/**
 * Class Ubuntu16_04
 * @package App\Services\Server\ProvisionRepositories
 */
class Ubuntu16_04 implements ProvisionSystemContract
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
        $this->remoteTaskService->ssh($server);
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

    public function addCodePierUser(Server $server, $sudoPassword)
    {
        $this->remoteTaskService->run('echo \'root:'.$sudoPassword.'\' | chpasswd');

        $this->remoteTaskService->run('adduser --disabled-password --gecos "" codepier');

        $this->remoteTaskService->run('echo \'codepier:'.$sudoPassword.'\' | chpasswd');
        $this->remoteTaskService->run('adduser codepier sudo');
        $this->remoteTaskService->run('usermod -a -G www-data codepier');

        $this->remoteTaskService->run('mkdir /home/codepier/.ssh && cp -a ~/.ssh/authorized_keys /home/codepier/.ssh/authorized_keys');
        $this->remoteTaskService->run('chmod 700 /home/codepier/.ssh && chmod 600 /home/codepier/.ssh/authorized_keys');

        $this->remoteTaskService->writeToFile('/home/codepier/.ssh/id_rsa', $server->private_ssh_key);
        $this->remoteTaskService->writeToFile('/home/codepier/.ssh/id_rsa.pub', $server->public_ssh_key);

        $this->remoteTaskService->updateText('/etc/ssh/sshd_config', '#PasswordAuthentication', 'PasswordAuthentication no');
        $this->remoteTaskService->updateText('/etc/ssh/sshd_config', 'PermitRootLogin', 'PermitRootLogin no');
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

        $this->remoteTaskService->run('sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php/7.0/cli/php.ini');
        $this->remoteTaskService->run('sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php/7.0/cli/php.ini');
    }

    public function installNginx()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y nginx');

        $this->remoteTaskService->run('rm /etc/nginx/sites-enabled/default');
        $this->remoteTaskService->run('rm /etc/nginx/sites-available/default');

        $this->remoteTaskService->run('sed -i "s/user www-data;/user codepier;/" /etc/nginx/nginx.conf');
        $this->remoteTaskService->run('sed -i "s/# server_names_hash_bucket_size.*/server_names_hash_bucket_size 64;/" /etc/nginx/nginx.conf');

        $this->remoteTaskService->run('mkdir -p /etc/nginx/codepier-conf');

        $this->remoteTaskService->run('openssl dhparam -out /etc/nginx/dhparam.pem 2048');

        $this->remoteTaskService->run('echo "fastcgi_param HTTP_PROXY \"\";" >> /etc/nginx/fastcgi_params');

        $this->remoteTaskService->run('service nginx restart');
        $this->remoteTaskService->run('service php7.0-fpm restart');
    }

    public function installPhpFpm()
    {
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

    public function installSupervisor()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y supervisor');
        $this->remoteTaskService->run('mkdir /home/codepier/workers');

        $this->remoteTaskService->run('service supervisor start');
    }

    public function installGit(Server $server)
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y git');

        $this->remoteTaskService->run('echo git config --global user.name "'.$server->user->name.'"');
        $this->remoteTaskService->run('echo git config --global user.email '.$server->user->email.'');
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
        $this->remoteTaskService->run('service beanstalkd restart');
    }

    public function installComposer()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y composer');
    }

    public function installLaravelInstaller()
    {
        $this->remoteTaskService->run('sudo su codepier; composer global require "laravel/installer=~1.1"');
    }

    public function installEnvoy()
    {
        $this->remoteTaskService->run('sudo su codepier; composer global require "laravel/envoy=~1.0"');
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

    public function installMySQL($databasePassword, $database = null)
    {
        $this->remoteTaskService->run("debconf-set-selections <<< 'mysql-server/root_password password $databasePassword'");
        $this->remoteTaskService->run("debconf-set-selections <<< 'mysql-server/root_password_again password $databasePassword'");

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server');

        $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e \"GRANT ALL ON *.* TO codepier@'%' IDENTIFIED BY '$databasePassword' WITH GRANT OPTION;\"");

        $this->remoteTaskService->run("mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=$databasePassword mysql");

        if(!empty($database)) {
            $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e 'CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8 COLLATE utf8_general_ci'");
        }
    }

    public function installMariaDB($databasePassword, $database = null)
    {
        $this->remoteTaskService->run("debconf-set-selections <<< 'maria-db-10.0 mysql-server/root_password password $databasePassword'");
        $this->remoteTaskService->run("debconf-set-selections <<< 'maria-db-10.0 mysql-server/root_password_again password $databasePassword'");

        $this->remoteTaskService->run('apt-get install -y mariadb-server');

        $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e \"GRANT ALL ON *.* TO codepier@'%' IDENTIFIED BY '$databasePassword' WITH GRANT OPTION;\"");

        $this->remoteTaskService->run("mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=$databasePassword mysql");

        if(!empty($database)) {
            $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e 'CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8 COLLATE utf8_general_ci'");
        }
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

    public function installFirewallRules(Server $server)
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y fail2ban iptables-persistent');

        ServerFirewallRule::create([
            'server_id' => $server->id,
            'description' => 'HTTP',
            'port' => '80',
            'from_ip' => null
        ]);

        ServerFirewallRule::create([
            'server_id' => $server->id,
            'description' => 'HTTPS',
            'port' => '443',
            'from_ip' => null
        ]);

        $this->remoteTaskService->writeToFile('/etc/opt/iptables','
#!/bin/sh

echo "REDOING IP TABLES"

iptables -F
iptables -X
iptables -t nat -F
iptables -t nat -X
iptables -t mangle -F
iptables -t mangle -X

iptables -P INPUT ACCEPT
iptables -P FORWARD ACCEPT
iptables -P OUTPUT ACCEPT

iptables -A INPUT -m conntrack --ctstate RELATED,ESTABLISHED -j ACCEPT
iptables -I INPUT 1 -i lo -j ACCEPT

# SSH
iptables -A INPUT -p tcp -m tcp --dport 22 -j ACCEPT

# DO NOT REMOVE - Custom Rules
iptables -A INPUT -p tcp -m tcp --dport 80 -j ACCEPT
iptables -A INPUT -p tcp -m tcp --dport 443 -j ACCEPT

iptables -P INPUT DROP
        ');

        $this->remoteTaskService->run('chmod 775 /etc/opt/iptables');
        $this->remoteTaskService->run('/etc/opt/./iptables');
        $this->remoteTaskService->run('iptables-save > /etc/iptables/rules.v4');
        $this->remoteTaskService->run('ip6tables-save > /etc/iptables/rules.v6');
    }

    public function addDiskMonitoringScript(Server $server)
    {
        $this->remoteTaskService->writeToFile('/etc/opt/diskusage','
df / | grep / | awk \'{ print $5 " " $6 }\' | while read output;
do
    usep=$(echo $output | awk \'{ print $1}\' | cut -d\'%\' -f1 )
    if [ $usep -ge 90 ]; then
        curl '.env('APP_URL').'/webhook/diskspace?key='.$server->encode().'
    fi
done');

        $this->remoteTaskService->run('chmod 775 /etc/opt/diskusage');

        $cronJob = '*/5 * * * * /etc/opt/./diskusage';

        $this->remoteTaskService->run('crontab -l | (grep ' . $cronJob . ') || ((crontab -l; echo "' . $cronJob . ' >/dev/null 2>&1") | crontab)');

    }
}