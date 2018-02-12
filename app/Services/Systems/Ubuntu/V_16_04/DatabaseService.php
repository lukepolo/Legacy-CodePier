<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Models\Schema;
use App\Models\SchemaUser;
use App\Exceptions\UnknownDatabase;
use App\Services\Systems\SystemService;
use App\Services\Systems\ServiceConstructorTrait;

class DatabaseService
{
    const MYSQL = 'MySQL';
    const REDIS = 'Redis';
    const MARIADB = 'MariaDB';
    const MONGODB = 'MongoDB';
    const MEMCACHED = 'Memcached';
    const POSTGRESQL = 'PostgreSQL';

    use ServiceConstructorTrait;

    /**
     * @description MariaDB is one of the most popular database servers in the world. It’s made by the original developers of MySQL and guaranteed to stay open source.
     *
     * @conflicts MySQL
     */
    public function installMariaDB()
    {
        $databasePassword = $this->server->database_password;

        $this->connectToServer();

        $this->remoteTaskService->run("debconf-set-selections <<< 'maria-db-10.0 mysql-server/root_password password $databasePassword'");
        $this->remoteTaskService->run("debconf-set-selections <<< 'maria-db-10.0 mysql-server/root_password_again password $databasePassword'");

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y mariadb-server');

        $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e \"GRANT ALL ON *.* TO codepier@'%' IDENTIFIED BY '$databasePassword' WITH GRANT OPTION;\"");

        $this->remoteTaskService->run("mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=$databasePassword mysql");

        $this->remoteTaskService->updateText('/etc/mysql/mariadb.conf.d/50-server.cnf', 'bind-address', 'bind-address            = 0.0.0.0');

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service mysql restart');
    }

    /**
     *  @description Free & open source, high-performance, distributed memory object caching system.
     */
    public function installMemcached()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y memcached');

        $this->remoteTaskService->updateText('/etc/memcached.conf ', '-l 127.0.0.1', '#-l 127.0.0.1');

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service memcached restart');
    }

    /**
     * @description MySQL is the world's most popular open source database.
     *
     * @conflicts MariaDB
     */
    public function installMySQL()
    {
        $databasePassword = $this->server->database_password;

        $this->connectToServer();

        $this->remoteTaskService->run("debconf-set-selections <<< 'mysql-server/root_password password $databasePassword'");
        $this->remoteTaskService->run("debconf-set-selections <<< 'mysql-server/root_password_again password $databasePassword'");

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server');

        $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e \"GRANT ALL ON *.* TO codepier@'%' IDENTIFIED BY '$databasePassword' WITH GRANT OPTION;\"");

        $this->remoteTaskService->run("mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=$databasePassword mysql");

        $this->remoteTaskService->updateText('/etc/mysql/mysql.conf.d/mysqld.cnf', 'bind-address', '#bind-address           = 127.0.0.1');

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service mysql restart');
    }

    /**
     * @description PostgreSQL is a powerful, open source object-relational database system.
     */
    public function installPostgreSQL()
    {
        $this->connectToServer();

        $databasePassword = $this->server->database_password;

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y postgresql');
        $this->remoteTaskService->run('sudo -u postgres psql -c "CREATE ROLE codepier LOGIN UNENCRYPTED PASSWORD \''.$databasePassword.'\' SUPERUSER INHERIT NOCREATEDB NOCREATEROLE NOREPLICATION;"');

        $this->remoteTaskService->run('service postgresql restart');
    }

    /**
     *  @description Redis is an open source (BSD licensed), in-memory data structure store, used as a database, cache and message broker.
     */
    public function installRedis()
    {
        $this->connectToServer();
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y redis-server');
        $this->remoteTaskService->updateText('/etc/redis/redis.conf', 'bind 127.0.0.1', '#bind 127.0.0.1');
        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service redis restart');
    }

    /**
     *  @description Cross-platform C library that implements a self-contained, embeddable, zero-configuration SQL database engine.
     */
    public function installSqlLite()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y sqlite');
    }

    /**
     *  @description MongoDB (3.6) is a free and open-source cross-platform document-oriented database program. Classified as a NoSQL database program, MongoDB uses JSON-like documents with schemas.
     */
    public function installMongoDB()
    {
        $databasePassword = $this->server->database_password;

        $this->connectToServer();

        $this->remoteTaskService->makeDirectory('/data/db');
        $this->remoteTaskService->run('apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 2930ADAE8CAF5059EE73BB4B58712A2291FA4AD5');
        $this->remoteTaskService->run('echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.6 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.6.list');
        $this->remoteTaskService->run('apt-get update');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y mongodb-org');
        $this->remoteTaskService->updateText('/etc/mongod.conf', 'bind_ip', '# bind_ip = 127.0.0.1');

        $this->remoteTaskService->run('service mongod start');

        sleep(2);

        $this->remoteTaskService->run("mongo --eval \"db.createUser({ user : 'codepier', pwd : '$databasePassword', roles : ['readWrite', 'dbAdmin'] });\"");

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service mongod restart');
    }

    /**
     * @param Schema $schema
     * @throws UnknownDatabase
     */
    public function addSchema(Schema $schema)
    {
        $this->connectToServer();

        $databasePassword = $this->server->database_password;
        $database = $schema->name;

        switch ($schema->database) {
            case self::MARIADB:
            case self::MYSQL:
                $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e 'CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'");
                break;
            case self::POSTGRESQL:
                $this->remoteTaskService->run("cd /home && sudo -u postgres /usr/bin/createdb --echo --owner=codepier $database --lc-collate=en_US.UTF-8 --lc-ctype=en_US.UTF-8");
                break;
            case self::MONGODB:
                // we don't need to create one , it will create itself based on the user permissions if needed
            default:
                throw new UnknownDatabase($schema->database);
                break;
        }
    }

    /**
     * @param Schema $schema
     * @throws UnknownDatabase
     */
    public function removeSchema(Schema $schema)
    {
        $this->connectToServer();

        $database = $schema->name;
        $databasePassword = $this->server->database_password;

        switch ($schema->database) {
            case self::MARIADB:
            case self::MYSQL:
                $this->remoteTaskService->run("mysql --user=root --password=$databasePassword -e 'DROP DATABASE $database'");
                break;
            case self::POSTGRESQL:
                $this->remoteTaskService->run('sudo -u postgres /usr/bin/dropdb '.$database);
                break;
            case self::MONGODB:
                $this->remoteTaskService->run("mongo -u codepier -p $databasePassword admin --eval \"db.dropDatabase('$database');\"");
                break;
            default:
                throw new UnknownDatabase($schema->database);
                break;
        }
    }

    /**
     * @param SchemaUser $schemaUser
     * @throws UnknownDatabase
     */
    public function addSchemaUser(SchemaUser $schemaUser)
    {
        $this->connectToServer();

        foreach ($schemaUser->schema_ids as $schemaId) {
            $schema = Schema::findOrFail($schemaId);
            switch ($schema->database) {
                case self::MARIADB:
                case self::MYSQL:
                    $this->addMySqlUser($schemaUser, $schema);
                    break;
                case self::POSTGRESQL:
                    $this->addPostgreSQLUser($schemaUser, $schema);
                    break;
                case self::MONGODB:
                    $this->addMongoDbUser($schemaUser, $schema);
                    break;
                default:
                    throw new UnknownDatabase($schema->database);
                    break;
            }
        }
    }

    /**
     * @param SchemaUser $schemaUser
     * @throws UnknownDatabase
     */
    public function removeSchemaUser(SchemaUser $schemaUser)
    {
        $this->connectToServer();

        foreach ($schemaUser->schema_ids as $schemaId) {
            $schema = Schema::findOrFail($schemaId);
            switch ($schema->database) {
                case self::MARIADB:
                case self::MYSQL:
                    $this->removeMySqlUser($schemaUser, $schema);
                    break;
                case self::POSTGRESQL:
                    $this->removePostgreSQLUser($schemaUser, $schema);
                    break;
                case self::MONGODB:
                    $this->removeMongoDbUser($schemaUser, $schema);
                    break;
                default:
                    throw new UnknownDatabase($schema->database);
                    break;
            }
        }
    }

    /**
     * @param $database
     * @return bool
     */
    private function hasDatabaseInstalled($database)
    {
        $databaseServices = $this->server->server_features['DatabaseService'];

        if (isset($databaseServices[$database]) && $databaseServices[$database]['enabled'] === true) {
            return true;
        }

        return false;
    }

    private function addMySqlUser(SchemaUser $schemaUser, Schema $schema)
    {
        $this->remoteTaskService->run('mysql --user=root --password='.$this->server->database_password." -e \"GRANT ALL ON $schema->name.* TO $schemaUser->name@'%' IDENTIFIED BY '$schemaUser->password' WITH GRANT OPTION;\"");
    }

    private function removeMySqlUser(SchemaUser $schemaUser)
    {
        $this->remoteTaskService->run('mysql --user=root --password='.$this->server->database_password." -e \"DROP USER IF EXISTS $schemaUser->name;\"");
    }

    private function addPostgreSQLUser(SchemaUser $schemaUser, Schema $schema)
    {
        $this->remoteTaskService->run("cd /home && sudo -u postgres psql -c \"CREATE ROLE $schemaUser->name LOGIN UNENCRYPTED PASSWORD '$schemaUser->password';\"");
        $this->remoteTaskService->run("cd /home && sudo -u postgres psql -c \"GRANT CONNECT ON DATABASE $schema->name TO $schemaUser->name;\"");
    }

    private function removePostgreSQLUser(SchemaUser $schemaUser, Schema $schema)
    {
        $this->remoteTaskService->run("cd /home && sudo -u postgres psql -c \"REVOKE ALL PRIVILEGES ON DATABASE $schema->name from $schemaUser->name;\"");
        $this->remoteTaskService->run("cd /home && sudo -u postgres psql -c \"DROP ROLE $schemaUser->name;\"");
    }

    private function addMongoDbUser(SchemaUser $schemaUser, Schema $schema)
    {
        $this->remoteTaskService->run('mongo -u codepier -p '.$this->server->database_password." admin --eval \"db.createUser({ user : '$schemaUser->name', pwd : '$schemaUser->password', roles : [ { role : 'readWrite', db: '$schema->name' } ] });\"");
    }

    private function removeMongoDbUser(SchemaUser $schemaUser)
    {
        $this->remoteTaskService->run('mongo -u codepier -p '.$this->server->database_password." admin --eval \"db.removeUser($schemaUser->name);\"");
    }
}
