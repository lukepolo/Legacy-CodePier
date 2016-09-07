<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Services\Systems\ServiceConstructorTrait;

class DatabaseService
{
    use ServiceConstructorTrait;

    public function installMariaDB($database = null)
    {
        $databasePassword = decrypt($this->server->database_password);

        $this->connectToServer();

        $this->remoteTaskService->run("debconf-set-selections <<< 'maria-db-10.0 mysql-server/root_password password $databasePassword'");
        $this->remoteTaskService->run("debconf-set-selections <<< 'maria-db-10.0 mysql-server/root_password_again password $databasePassword'");

        $this->remoteTaskService->run('apt-get install -y mariadb-server');

        $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e \"GRANT ALL ON *.* TO codepier@'%' IDENTIFIED BY '$databasePassword' WITH GRANT OPTION;\"");

        $this->remoteTaskService->run("mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=$databasePassword mysql");

        if (! empty($database)) {
            $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e 'CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8 COLLATE utf8_general_ci'");
        }
    }

    public function installMemcached()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y memcached');
    }

    public function installMySQL($database = null)
    {
        $databasePassword = decrypt($this->server->database_password);

        $this->connectToServer();

        $this->remoteTaskService->run("debconf-set-selections <<< 'mysql-server/root_password password $databasePassword'");
        $this->remoteTaskService->run("debconf-set-selections <<< 'mysql-server/root_password_again password $databasePassword'");

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server');

        $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e \"GRANT ALL ON *.* TO codepier@'%' IDENTIFIED BY '$databasePassword' WITH GRANT OPTION;\"");

        $this->remoteTaskService->run("mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=$databasePassword mysql");

        if (! empty($database)) {
            $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e 'CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8 COLLATE utf8_general_ci'");
        }
    }

    public function installPostgreSQL()
    {
        $databasePassword = decrypt($this->server->database_password);
    }

    public function installRedis()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y redis-server');
    }

    public function installMongoDB()
    {
        $this->connectToServer();

        $this->remoteTaskService->makeDirectory('/data/db');
        $this->remoteTaskService->run('apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv EA312927');
        $this->remoteTaskService->run('echo "deb http://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.2 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.2.list');
        $this->remoteTaskService->run('apt-get update');
        $this->remoteTaskService->run('apt-get install -y mongodb-org php-mongodb ');
    }

    public function restartDatabase()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('service mysql restart');
    }
}
