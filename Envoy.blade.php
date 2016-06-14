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

    $repository = 'git@github.com:lukepolo/CodePier.git';

    $now = new DateTime();
    $date = $now->format('YmdHis');

    $branch = isset($branch) ? $branch : null;
    $path = isset($path) ? rtrim($path, '/') : null;
    $release = isset($path) ? $path.'/'.$date : null;
    $domain = isset($domain) ? $domain : null;
@endsetup

@servers(['web' => '-o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no '.$options['user'].'@'.$options['server']])

@macro('provision')
    add_codepier_user
    install_ppas
    basic_packages
    server_settings
    php_packages
    install_laravel_packages
    install_nginx_fpm
    install_node
    install_mysql
    create_swap
@endmacro

@macro('deploy')
    updating_repository
    updating_third_party_vendors
    setup_folders
    migrations
    cleanup
@endmacro

@task('create_site')
cat > /etc/nginx/sites-enabled/{{ $domain }} <<'EOF'

    # codeier CONFIG (DOT NOT REMOVE!)
    #include codeier-conf/{{ $domain }}/before/*;

    server {
        listen 80;
        server_name {{ $domain }};
        root /home/codepier/{{ $domain }}/current/public;

        index index.html index.htm index.php;

        charset utf-8;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }

        access_log off;
        error_log  /var/log/nginx/{{ $domain }}-error.log error;

        sendfile off;

        client_max_body_size 100m;

        location ~ \.php$ {
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

            fastcgi_intercept_errors off;
            fastcgi_buffer_size 16k;
            fastcgi_buffers 4 16k;
            fastcgi_connect_timeout 300;
            fastcgi_send_timeout 300;
            fastcgi_read_timeout 300;
        }

        location ~ /\.ht {
            deny all;
        }
    }

    # codeier CONFIG (DOT NOT REMOVE!)
    #include codeier-conf/{{ $domain }}/after/*;
EOF

service nginx restart;
@endtask

@task('updating_repository')

    @if(!file_exists($path))
        mkdir -p {{ $path }}
    @endif

    echo "Installing into {{ $path }}";
    cd {{ $path }};
    git clone {{ $repository }} --branch={{ $branch }} --depth=1 {{ $release }};
    echo "Repository fetched";

    @if(!file_exists($path.'/.env'))
        cp {{ $release }}/.env.example {{ $path }}/.env;
    @endif

    ln -s {{ $path }}/.env {{ $release }}/.env;
    echo "Environment file set up";
@endtask

@task('updating_third_party_vendors')
    cd {{ $release }}

    composer install --no-interaction;
    echo "Composer / Framework optimizations complete";

    @if(!file_exists($path.'/node_modules'))
        npm install --production;
        mv {{ $release }}/node_modules {{ $path }}/node_modules;
        echo "Installed Node Modules";
    @endif

    ln -s {{ $path }}/node_modules {{ $release }}/node_modules;

    echo "Node modules set up";
@endtask

@task('setup_folders')
    ln -sfn {{ $release }} {{ $path }}/current;
    echo "Production folder set up : {{ $release }}";
@endtask

@task('migrations')
    cd {{ $release }}

    php artisan migrate --force --no-interaction;
    echo "Migrations complete";

    php artisan queue:restart

    echo "Queue Restarted"
@endtask

@task('cleanup')
    cd {{ $path }};
    find . -maxdepth 1 -name "2*" -mmin +2880 | sort | head -n {{ 10 }} | xargs rm -Rf;
    echo "Cleaned up old deployments";
@endtask

@task('add_codepier_user')
    adduser --disabled-password --gecos "" codepier
    echo 'codepier:mypass' | chpasswd
    adduser codepier sudo
    usermod -a -G www-data codepier

    # Allow user to login as codepier
    mkdir /home/codepier/.ssh && cp -a ~/.ssh/authorized_keys /home/codepier/.ssh/authorized_keys
    chmod 700 /home/codepier/.ssh && chmod 600 /home/codepier/.ssh/authorized_keys

    # Generate ssh key for codepier
    ssh-keygen -t rsa -N "" -f ~/.ssh/id_rsa

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
@endtask

@task('basic_packages')
    apt-get update
    apt-get install -y zip unzip git supervisor redis-server memcached beanstalkd

    # Configure Beanstalkd
    sed -i "s/#START=yes/START=yes/" /etc/default/beanstalkd
    /etc/init.d/beanstalkd start
@endtask

@task('server_settings')
    # Set My Timezone
    ln -sf /usr/share/zoneinfo/UTC /etc/localtime
@endtask

@task('php_packages')
    apt-get update
    apt-get install -y php7.0-cli php7.0-dev php-pgsql php-sqlite3 php-gd php-apcu php-curl php7.0-mcrypt php-imap php-mysql php-memcached php7.0-readline php-mbstring php-dom php-xml php7.0-zip php7.0-intl php7.0-bcmath php-soap composer

    # Set Some PHP CLI Settings

    sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/7.0/cli/php.ini
    sed -i "s/display_errors = .*/display_errors = On/" /etc/php/7.0/cli/php.ini
    sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php/7.0/cli/php.ini
    sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php/7.0/cli/php.ini

@endtask

@task('install_laravel_packages')
    sudo su codepier <<'EOF'
        composer global require "laravel/envoy=~1.0"
        composer global require "laravel/installer=~1.1"
    EOF
@endtask

@task('install_nginx_fpm')
    apt-get update
    apt-get install -y --force-yes nginx php7.0-fpm

    rm /etc/nginx/sites-enabled/default
    rm /etc/nginx/sites-available/default

    sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/7.0/fpm/php.ini
    sed -i "s/display_errors = .*/display_errors = On/" /etc/php/7.0/fpm/php.ini
    sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/7.0/fpm/php.ini
    sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php/7.0/fpm/php.ini
    sed -i "s/upload_max_filesize = .*/upload_max_filesize = 100M/" /etc/php/7.0/fpm/php.ini
    sed -i "s/post_max_size = .*/post_max_size = 100M/" /etc/php/7.0/fpm/php.ini
    sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php/7.0/fpm/php.ini

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
    apt-get update
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

    mysql --user="root" --password="secret" -e "GRANT ALL ON *.* TO root@'%' IDENTIFIED BY 'secret' WITH GRANT OPTION;"
    service mysql restart

    # Add Timezone Support To MySQL
    mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql --user=root --password=secret mysql
@endtask

@task('create_swap')
    fallocate -l 1G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    cp /etc/fstab /etc/fstab.bak
    echo '/swapfile none swap sw 0 0' | tee -a /etc/fstab
    echo "vm.swappiness=10" >> /etc/sysctl.conf
    echo "vm.vfs_cache_pressure=50" >> /etc/sysctl.conf
@endtask

@after

@endafter