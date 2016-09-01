<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Services\Systems\Traits\ServiceConstructorTrait;

class DatabaseService
{
    use ServiceConstructorTrait;

    public function installDatabases()
    {
        $this->connectToServer();

        $databasePassword = decrypt($this->server->database_password);

        $database = isset($this->server->options['database']) ? $this->server->options['database'] : null;

        if ($this->server->hasFeature('mariaDB')) {
            $this->installMariaDB($databasePassword, $database);
        } else {
            $this->installMySQL($databasePassword, $database);
        }
    }

    public function installRedis()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y redis-server');
    }

    public function installMemcached()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y memcached');
    }

    public function installMySQL($databasePassword, $database = null)
    {
        $this->connectToServer();

        $this->remoteTaskService->run("debconf-set-selections <<< 'mysql-server/root_password password $databasePassword'");
        $this->remoteTaskService->run("debconf-set-selections <<< 'mysql-server/root_password_again password $databasePassword'");

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server');

        $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e \"GRANT ALL ON *.* TO codepier@'%' IDENTIFIED BY '$databasePassword' WITH GRANT OPTION;\"");

        $this->remoteTaskService->run("mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=$databasePassword mysql");

        if (!empty($database)) {
            $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e 'CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8 COLLATE utf8_general_ci'");
        }
    }

    public function installMariaDB($databasePassword, $database = null)
    {
        $this->connectToServer();

        $this->remoteTaskService->run("debconf-set-selections <<< 'maria-db-10.0 mysql-server/root_password password $databasePassword'");
        $this->remoteTaskService->run("debconf-set-selections <<< 'maria-db-10.0 mysql-server/root_password_again password $databasePassword'");

        $this->remoteTaskService->run('apt-get install -y mariadb-server');

        $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e \"GRANT ALL ON *.* TO codepier@'%' IDENTIFIED BY '$databasePassword' WITH GRANT OPTION;\"");

        $this->remoteTaskService->run("mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=$databasePassword mysql");

        if (!empty($database)) {
            $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e 'CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8 COLLATE utf8_general_ci'");
        }
    }

    public function restartDatabase()
    {
        $this->connectToServer();

        return $this->remoteTaskService->run('service mysql restart');
    }
}
