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

    $branch = isset($branch) ? $branch : null;
    $path = isset($path) ? rtrim($path, '/') : null;
    $release = isset($path) ? $path.'/'.$date : null;
@endsetup

@servers(['web' => '-o StrictHostKeyChecking=no root@'.$options['server']])

@macro('provision')
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


    sed -i "s/user www-data;/user codepier;/" /etc/nginx/nginx.conf
    sed -i "s/# server_names_hash_bucket_size.*/server_names_hash_bucket_size 64;/" /etc/nginx/nginx.conf

    sed -i "s/user = www-data/user = codepier/" /etc/php/7.0/fpm/pool.d/www.conf
    sed -i "s/group = www-data/group = codepier/" /etc/php/7.0/fpm/pool.d/www.conf

    sed -i "s/listen\.owner.*/listen.owner = codepier/" /etc/php/7.0/fpm/pool.d/www.conf
    sed -i "s/listen\.group.*/listen.group = codepier/" /etc/php/7.0/fpm/pool.d/www.conf
    sed -i "s/;listen\.mode.*/listen.mode = 0666/" /etc/php/7.0/fpm/pool.d/www.conf


    # Disable XDebug On The CLI

    sudo phpdismod -s cli xdebug

    # Set The Nginx & PHP-FPM User

    service nginx restart
    service php7.0-fpm restart
@endtask

@task('install_node')
    apt-get install -y nodejs
    /usr/bin/npm install -g gulp
    /usr/bin/npm install -g bower
@endtask

@task('install_mysql')
    export DEBIAN_FRONTEND="noninteractive"
    debconf-set-selections <<< 'mysql-server mysql-server/root_password password secret'
    debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password secret'

    apt-get install -y mysql-server

    # Configure MySQL Remote Access
    sed -i '/^bind-address/s/bind-address.*=.*/bind-address = 0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf

    mysql --user="root" --password="secret" -e "GRANT ALL ON *.* TO root@'0.0.0.0' IDENTIFIED BY 'secret' WITH GRANT OPTION;"
    service mysql restart

    # Add Timezone Support To MySQL
    mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=secret mysql
@endtask