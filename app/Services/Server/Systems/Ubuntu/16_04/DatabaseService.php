<?php

namespace App\Services\Server\ProvisionSystems;

class DatabaseService
{
    public function installRedis()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y redis-server');

    }

    public function installMemcached()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y memcached');
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

    /**
     * @param Server $server
     * @param string $user
     * @return bool
     */
    public function restartDatabase(Server $server, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        // TODO - restart maria db
        return $this->remoteTaskService->run('service mysql restart');

    }
}