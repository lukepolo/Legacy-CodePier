<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Services\Systems\SystemService;
use App\Services\Systems\ServiceConstructorTrait;

class DatabaseService
{
    use ServiceConstructorTrait;

    public function installMariaDB($database = null)
    {
        $databasePassword = $this->server->database_password;

        $this->connectToServer();

        $this->remoteTaskService->run("debconf-set-selections <<< 'maria-db-10.0 mysql-server/root_password password $databasePassword'");
        $this->remoteTaskService->run("debconf-set-selections <<< 'maria-db-10.0 mysql-server/root_password_again password $databasePassword'");

        $this->remoteTaskService->run('apt-get install -y mariadb-server');

        $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e \"GRANT ALL ON *.* TO codepier@'%' IDENTIFIED BY '$databasePassword' WITH GRANT OPTION;\"");
        $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e \"GRANT ALL ON *.* TO codepier_servers@'%' IDENTIFIED BY '$databasePassword' WITH GRANT OPTION;\"");

        $this->remoteTaskService->run("mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=$databasePassword mysql");

        if (! empty($database)) {
            $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e 'CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'");
        }

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service mysql restart');
    }

    public function installMemcached()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y memcached');

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service memcached restart');
    }

    public function installMySQL($database = null)
    {
        $databasePassword = $this->server->database_password;

        $this->connectToServer();

        $this->remoteTaskService->run("debconf-set-selections <<< 'mysql-server/root_password password $databasePassword'");
        $this->remoteTaskService->run("debconf-set-selections <<< 'mysql-server/root_password_again password $databasePassword'");

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server');

        $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e \"GRANT ALL ON *.* TO codepier@'%' IDENTIFIED BY '$databasePassword' WITH GRANT OPTION;\"");
        $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e \"GRANT ALL ON *.* TO codepier_servers@'%' IDENTIFIED BY '$databasePassword' WITH GRANT OPTION;\"");

        $this->remoteTaskService->run("mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=$databasePassword mysql");

        if (! empty($database)) {
            $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e 'CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'");
        }

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service mysql restart');
    }

    public function installPostgreSQL($database = null)
    {
        $this->connectToServer();

        $databasePassword = $this->server->database_password;

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y postgresql');
        $this->remoteTaskService->run('sudo -u postgres psql -c "CREATE ROLE codepier LOGIN UNENCRYPTED PASSWORD \''.$databasePassword.'\' SUPERUSER INHERIT NOCREATEDB NOCREATEROLE NOREPLICATION;"');
        $this->remoteTaskService->run('sudo -u postgres psql -c "CREATE ROLE codepier_servers LOGIN UNENCRYPTED PASSWORD \''.$databasePassword.'\' SUPERUSER INHERIT NOCREATEDB NOCREATEROLE NOREPLICATION;"');

        if (! empty($database)) {
            $this->remoteTaskService->run('sudo -u postgres /usr/bin/createdb --echo --owner=codepier '.$database.' --locale=UTF8 --lc-collate=en_US.UTF-8 --lc-ctype=en_US.UTF-8');
        }

        $this->remoteTaskService->run('service postgresql restart');
    }

    public function installRedis()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y redis-server');

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service redis restart');
    }

    public function installSqlLite()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y sqlite');
    }

    public function installMongoDB()
    {
        $this->connectToServer();

        $this->remoteTaskService->makeDirectory('/data/db');
        $this->remoteTaskService->run('apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv EA312927');
        $this->remoteTaskService->run('echo "deb http://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.2 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.2.list');
        $this->remoteTaskService->run('apt-get update');
        $this->remoteTaskService->run('apt-get install -y mongodb-org php-mongodb ');
        $this->remoteTaskService->run('service mongod start');

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service mongod restart');
    }
}
