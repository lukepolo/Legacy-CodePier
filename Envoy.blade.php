@setup
    $options = [];
    foreach ($_SERVER['argv'] as $argument) {
    if (! starts_with($argument, '--')) {
        continue;
    }

    $option = explode('=', substr($argument, 2));
        if (count($option) == 1) {
            $option[1] = true;
        }
        $options[$option[0]] = $option[1];
    }

    $repo = 'https://github.com/laravel/laravel.git';

    $now = new DateTime();
    $date = $now->format('YmdHis');
    $branch = $branch;
    $path = rtrim($path, '/');
    $release = $path.'/'.$date;
@endsetup

@servers(['web' => 'root@'.$options['server']])

@macro('deploy')
    updating_repository
    updating_third_party_vendors
    migrations
@endmacro

@task('updating_repository')

    @if(!file_exists($path))
        mkdir -p {{ $path }}
    @endif

    cd {{ $path }};
    git clone {{ $repo }} --branch={{ $branch }} --depth=1 {{ $release }};
    echo "Repository fetched";

    ln -s {{ $path }}/.env {{ $release }}/.env;
    echo "Environment file set up";
@endtask

@task('updating_third_party_vendors')
    cd {{ $release }}

    composer install --no-interaction;
    echo "Composer / Framework optimizations complete";

    @if(!file_exists($path.'/node_modules'))
        npm install;
        mv {{ $release }}/node_modules {{ $path }}/node_modules;
    @else
        rm -rf {{ $release }}/node_modules;
    @endif

    ln -s {{ $path }}/node_modules {{ $release }}/node_modules;

    echo "Node modules set up";

@endtask

@task('migrations')

    @if(file_exists($path.'/current'))
        rm {{ $path }}/current;
    @endif

    ln -s {{ $release }} {{ $path }}/current;
    echo "Production folder set up";

    cd {{ $release }}

    php artisan migrate --force --no-interaction;
    echo "Migrations complete";

    php artisan queue:restart
    echo "Queue Restarted"
@endtask

@task('cleanup')
    cd {{ $path }};
    find . -maxdepth 1 -name "2*" -mmin +2880 | sort | head -n {{ 10 }} | xargs rm -Rf;
    echo "Cleaned up old deploments";
@endtask

@macro('provision')
    DEBIAN_FRONTEND=noninteractive
    add_codepier_user
    install_ppas
    basic_packages
    server_settings
    php_packages
    install_composer
    install_laravel_packages
    install_nginx_fpm
    install_node
    install_mysql
@endmacro

@task('add_codepier_user')
    adduser --disabled-password --gecos "" codepier
    echo 'codepier:mypass' | chpasswd
    adduser codepier sudo
    usermod -a -G www-data codepier

    mkdir /home/codepier/.ssh && cp -a ~/.ssh/authorized_keys /home/codepier/.ssh/authorized_keys
    chmod 700 /home/codepier/.ssh && chmod 600 /home/codepier/.ssh/authorized_keys
    chown codepier /home/codepier/.ssh -R
@endtask

@task('install_ppas')
    # Update Package List
    apt-get update

    # Update System Packages
    apt-get -y upgrade

    # Force Locale
    echo "LC_ALL=en_US.UTF-8" >> /etc/default/locale
    locale-gen en_US.UTF-8

    # Install Some PPAs
    apt-get install -y software-properties-common curl

    apt-add-repository ppa:nginx/development -y
    apt-add-repository ppa:chris-lea/redis-server -y
    apt-add-repository ppa:ondrej/php -y

    # gpg: key 5072E1F5: public key "MySQL Release Engineering <mysql-build@oss.oracle.com>" imported
    apt-key adv --keyserver ha.pool.sks-keyservers.net --recv-keys 5072E1F5
    sh -c 'echo "deb http://repo.mysql.com/apt/ubuntu/ trusty mysql-5.7" >> /etc/apt/sources.list.d/mysql.list'

    #wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -
    #sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt/ trusty-pgdg main" >> /etc/apt/sources.list.d/postgresql.list'

    curl --silent --location https://deb.nodesource.com/setup_6.x | bash -

    # Update Package Lists
    apt-get update
@endtask

@task('basic_packages')
    apt-get install -y git supervisor redis-server memcached beanstalkd

    # Configure Beanstalkd
    sed -i "s/#START=yes/START=yes/" /etc/default/beanstalkd
    /etc/init.d/beanstalkd start
@endtask

@task('server_settings')
    # Set My Timezone
    ln -sf /usr/share/zoneinfo/UTC /etc/localtime

    # Enable Swap Memory
    /bin/dd if=/dev/zero of=/var/swap.1 bs=1M count=1024
    /sbin/mkswap /var/swap.1
    /sbin/swapon /var/swap.1
@endtask

@task('php_packages')
    apt-get install -y php7.0-cli php7.0-dev \
    php-pgsql php-sqlite3 php-gd php-apcu \
    php-curl php7.0-mcrypt \
    php-imap php-mysql php-memcached php7.0-readline php-xdebug \
    php-mbstring php-xml php7.0-zip php7.0-intl php7.0-bcmath php-soap

    # Set Some PHP CLI Settings

    sudo sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/7.0/cli/php.ini
    sudo sed -i "s/display_errors = .*/display_errors = On/" /etc/php/7.0/cli/php.ini
    sudo sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php/7.0/cli/php.ini
    sudo sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php/7.0/cli/php.ini
@endtask

@task('install_composer')
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer

    # Add Composer Global Bin To Path
    printf "\nPATH=\"$(sudo su - codepier -c 'composer config -g home 2>/dev/null')/vendor/bin:\$PATH\"\n" | tee -a /home/codepier/.profile
@endtask

@task('install_laravel_packages')
    sudo su codepier <<'EOF'
    /usr/local/bin/composer global require "laravel/envoy=~1.0"
    /usr/local/bin/composer global require "laravel/installer=~1.1"
    EOF
@endtask

@task('install_nginx_fpm')
    apt-get install -y --force-yes nginx php7.0-fpm

    rm /etc/nginx/sites-enabled/default
    rm /etc/nginx/sites-available/default
    service nginx restart

    echo "xdebug.remote_enable = 1" >> /etc/php/7.0/fpm/conf.d/20-xdebug.ini
    echo "xdebug.remote_connect_back = 1" >> /etc/php/7.0/fpm/conf.d/20-xdebug.ini
    echo "xdebug.remote_port = 9000" >> /etc/php/7.0/fpm/conf.d/20-xdebug.ini
    echo "xdebug.max_nesting_level = 512" >> /etc/php/7.0/fpm/conf.d/20-xdebug.ini

    sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/7.0/fpm/php.ini
    sed -i "s/display_errors = .*/display_errors = On/" /etc/php/7.0/fpm/php.ini
    sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/7.0/fpm/php.ini
    sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php/7.0/fpm/php.ini
    sed -i "s/upload_max_filesize = .*/upload_max_filesize = 100M/" /etc/php/7.0/fpm/php.ini
    sed -i "s/post_max_size = .*/post_max_size = 100M/" /etc/php/7.0/fpm/php.ini
    sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php/7.0/fpm/php.ini

    # Disable XDebug On The CLI

    sudo phpdismod -s cli xdebug

    # Copy fastcgi_params to Nginx because they broke it on the PPA

    cat > /etc/nginx/fastcgi_params << EOF
    fastcgi_param	QUERY_STRING		\$query_string;
    fastcgi_param	REQUEST_METHOD		\$request_method;
    fastcgi_param	CONTENT_TYPE		\$content_type;
    fastcgi_param	CONTENT_LENGTH		\$content_length;
    fastcgi_param	SCRIPT_FILENAME		\$request_filename;
    fastcgi_param	SCRIPT_NAME		\$fastcgi_script_name;
    fastcgi_param	REQUEST_URI		\$request_uri;
    fastcgi_param	DOCUMENT_URI		\$document_uri;
    fastcgi_param	DOCUMENT_ROOT		\$document_root;
    fastcgi_param	SERVER_PROTOCOL		\$server_protocol;
    fastcgi_param	GATEWAY_INTERFACE	CGI/1.1;
    fastcgi_param	SERVER_SOFTWARE		nginx/\$nginx_version;
    fastcgi_param	REMOTE_ADDR		\$remote_addr;
    fastcgi_param	REMOTE_PORT		\$remote_port;
    fastcgi_param	SERVER_ADDR		\$server_addr;
    fastcgi_param	SERVER_PORT		\$server_port;
    fastcgi_param	SERVER_NAME		\$server_name;
    fastcgi_param	HTTPS			\$https if_not_empty;
    fastcgi_param	REDIRECT_STATUS		200;
    EOF


    # Set The Nginx & PHP-FPM User

    sed -i "s/user www-data;/user codepier;/" /etc/nginx/nginx.conf
    sed -i "s/# server_names_hash_bucket_size.*/server_names_hash_bucket_size 64;/" /etc/nginx/nginx.conf

    sed -i "s/user = www-data/user = codepier/" /etc/php/7.0/fpm/pool.d/www.conf
    sed -i "s/group = www-data/group = codepier/" /etc/php/7.0/fpm/pool.d/www.conf

    sed -i "s/listen\.owner.*/listen.owner = codepier/" /etc/php/7.0/fpm/pool.d/www.conf
    sed -i "s/listen\.group.*/listen.group = codepier/" /etc/php/7.0/fpm/pool.d/www.conf
    sed -i "s/;listen\.mode.*/listen.mode = 0666/" /etc/php/7.0/fpm/pool.d/www.conf

    service nginx restart
    service php7.0-fpm restart
@endtask

@task('install_node')
    apt-get install -y nodejs
    /usr/bin/npm install -g gulp
    /usr/bin/npm install -g bower
@endtask

@task('install_mysql')
    debconf-set-selections <<< "mysql-community-server mysql-community-server/data-dir select ''"
    debconf-set-selections <<< "mysql-community-server mysql-community-server/root-pass password secret"
    debconf-set-selections <<< "mysql-community-server mysql-community-server/re-root-pass password secret"
    apt-get install -y mysql-server

    # Configure MySQL Password Lifetime
    echo "default_password_lifetime = 0" >> /etc/mysql/my.cnf

    # Configure MySQL Remote Access
    sed -i '/^bind-address/s/bind-address.*=.*/bind-address = 0.0.0.0/' /etc/mysql/my.cnf

    mysql --user="root" --password="secret" -e "GRANT ALL ON *.* TO root@'0.0.0.0' IDENTIFIED BY 'secret' WITH GRANT OPTION;"
    service mysql restart


    # Add Timezone Support To MySQL
    mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=secret mysql
@endtask