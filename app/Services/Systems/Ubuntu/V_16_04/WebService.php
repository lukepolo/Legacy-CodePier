<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Models\Site\Site;
use App\Services\Server\ServerService;
use App\Services\Systems\SystemService;
use App\Services\Systems\ServiceConstructorTrait;

/**
 * // TODO - need to separate Apache and NGINX configs.
 */
class WebService
{
    use ServiceConstructorTrait;

    public static $files = [
        'Nginx' => [
            '/etc/nginx/nginx.conf',
        ],
    ];

    const NGINX_SERVER_FILES = '/etc/nginx/codepier-conf';

//    public function installApache()
//    {
//    }

    /**
     * @description Automatically enable HTTPS on your website with EFF's Certbot, deploying Let's Encrypt certificates.
     */
    public function installCertBot()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y letsencrypt');

        $this->remoteTaskService->writeToFile('/opt/codepier/lets_encrypt_renewals', '
letsencrypt renew | grep Congratulations &> /dev/null

if [ $? == 0 ]; then
    curl "' . config('app.url_stats') . '/webhook/server/' . $this->server->encode() . '/ssl/updated/"
fi
');

        $this->remoteTaskService->run('chmod 775 /opt/codepier/lets_encrypt_renewals');
    }

    /**
     * @description NGINX is a free, open-source, high-performance HTTP server and reverse proxy, as well as an IMAP/POP3 proxy server.
     */
    public function installNginx($workerProcesses = 'auto', $workerConnections = 'auto')
    {
        $this->connectToServer();

        if (! is_numeric($workerProcesses)) {
            $workerProcesses = $this->remoteTaskService->run('grep processor /proc/cpuinfo | wc -l');
        }

        if (! is_numeric($workerConnections)) {
            $workerConnections = $this->remoteTaskService->run('ulimit -n') * 2;
        }

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y nginx');

        $this->remoteTaskService->removeFile('/etc/nginx/sites-enabled/default');
        $this->remoteTaskService->removeFile('/etc/nginx/sites-available/default');

        $this->remoteTaskService->updateText('/etc/nginx/nginx.conf', 'worker_processes', "worker_processes $workerProcesses;");
        $this->remoteTaskService->updateText('/etc/nginx/nginx.conf', 'worker_connections', "worker_connections $workerConnections;");

        $this->remoteTaskService->updateText('/etc/nginx/nginx.conf', 'gzip off', 'gzip off;');
        $this->remoteTaskService->updateText('/etc/nginx/nginx.conf', 'gzip_vary', 'gzip_vary on;');
        $this->remoteTaskService->updateText('/etc/nginx/nginx.conf', 'gzip_proxied', 'gzip_proxied any;');
        $this->remoteTaskService->updateText('/etc/nginx/nginx.conf', 'gzip_comp_level', 'gzip_comp_level 6;');
        $this->remoteTaskService->updateText('/etc/nginx/nginx.conf', 'gzip_buffers', 'gzip_buffers 16 8k;');
        $this->remoteTaskService->updateText('/etc/nginx/nginx.conf', 'gzip_http_version', 'gzip_http_version 1.1;');
        $this->remoteTaskService->removeLineByText('/etc/nginx/nginx.conf', 'gzip_min_length');
        $this->remoteTaskService->findTextAndAppend('/etc/nginx/nginx.conf', 'gzip_http_version', 'gzip_min_length 256;');
        $this->remoteTaskService->updateText('/etc/nginx/nginx.conf', 'gzip_types', 'gzip_types application/atom+xml application/javascript application/json application/rss+xml application/vnd.ms-fontobject application/x-font-ttf application/x-web-app-manifest+json application/xhtml+xml application/xml font/opentype image/svg+xml image/x-icon text/css text/plain text/x-component;');

        $this->remoteTaskService->updateText('/etc/nginx/nginx.conf', 'user www-data', 'user codepier;');
        $this->remoteTaskService->updateText('/etc/nginx/nginx.conf', 'server_names_hash_bucket_size', 'server_names_hash_bucket_size 64;');
        $this->remoteTaskService->findTextAndAppend('/etc/nginx/nginx.conf', 'server_names_hash_bucket_size', 'proxy_headers_hash_max_size 128;');
        $this->remoteTaskService->findTextAndAppend('/etc/nginx/nginx.conf', 'ssl_prefer_server_ciphers', 'ssl_session_cache shared:SSL:50m;');

        $this->remoteTaskService->writeToFile('/etc/nginx/sites-enabled/catch-all', '
server {
    root /opt/codepier/landing;
}');

        $this->remoteTaskService->writeToFile('/opt/codepier/landing/index.html', '
<!doctype html>
<html lang="">

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title></title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:700" rel="stylesheet">
  <style>
    body {
      background-image: linear-gradient(to right top, #ffffff, #fff5fe, #ffeae8, #ffe7c6, #e5edb3);
    }

    .docked {
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      background: url("https://cdn.codepier.io/assets/img/docked-with-codepier.png") no-repeat center center fixed;
      background-size: cover;
      background-position: left top;
    }

    h1 {
      font-family: \'Open Sans\', \'Helvetica Neue\', \'Helvetica\', Arial, sans-serif;
      position: absolute;
      top: 30%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 3vw;
      text-align: center;
      color: #3C3F41;
    }
  </style>
</head>

<body>
  <div class="docked">
    <h1>Docked With CodePier</h1>
  </div>
</body>

</html>');

        $this->remoteTaskService->run('mkdir -p /etc/nginx/codepier-conf');

        $this->remoteTaskService->appendTextToFile('/etc/nginx/fastcgi_params', 'fastcgi_param HTTP_PROXY "";');

        $this->remoteTaskService->run('service nginx stop');

        $this->remoteTaskService->writeToFile('/etc/nginx/dhparam.pem', '-----BEGIN DH PARAMETERS-----
MIIBCAKCAQEA5M2MrvvA978Z4Zz6FBf/1CUZA3QcJyCUmeMwPVWBeTS9M3XJTYUY
Hr7UXZQtzWF5o3GLC2SAMzVVHGaJQDnruxBT5HLsneFpSZz5ntCq4tLLIE32dyYd
Vd/K+Mp1Cee3lw57iK/ZC/CfxoZ5qtWJ9/CRmfXwS8QMwmLl+pR8v5m0I4TqzgRM
1HEbY1YvgKNiy24HbOhr62Von27Fa8IpGVVhLjoL6VTNaGjh64vtbMZzp1Va9G5P
rPJFzPmaWrfBecGIEWEN77NLT8ieYpiLUw0s4PgnlM6Pijax/Z/YsqsZpN8nvmDc
gQw5FUmzayuEHRxRIy1uQ6qkPRThOrGQswIBAg==
-----END DH PARAMETERS-----');

        $this->remoteTaskService->run('service nginx restart');

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'nginx -t && service nginx reload');
    }

    /**
     * @param Site $site
     * @param $serverType
     * @throws \Exception
     */
    public function updateWebServerConfig(Site $site, $serverType)
    {
        $this->connectToServer();

        $activeSsl = false;

        if ($site->hasActiveSSL()) {
            $activeSsl = $site->activeSsl();

            if ($activeSsl->servers->count() && $activeSsl->servers->pluck('id')->contains($this->server->id)) {
            } else {
                $activeSsl = false;
            }
        }

        if ($serverType === SystemService::LOAD_BALANCER) {
            $upstreamName = snake_case(str_replace('.', '_', $site->domain));

            // TODO - for now we will do it by what servers are connected to that site
            // realistically it would be awesome if we could hook up cloudflares free anycast to allow for global load balancing
            $this->remoteTaskService->writeToFile(self::NGINX_SERVER_FILES . '/' . $site->domain . '/before/load-balancer', '
upstream ' . $upstreamName . ' {
    ip_hash;
    ' . $site->servers->map(function ($server) {
                $server->ip = 'server ' . $server->ip . ';';

                return $server;
            })->filter(function ($server) {
                return $server->type === SystemService::WEB_SERVER;
            })->implode('ip', "\n") . '
}');

            $location = '
location / {
    include proxy_params;
    proxy_pass http://'.$upstreamName.';
    proxy_redirect off;
    
    # Handle Web Socket connections
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
}
';
        } else {
            $location = 'root /home/codepier/'.$site->domain.($site->zero_downtime_deployment ? '/current' : null).'/'.$site->web_directory.';';
        }

        $this->createWebServerSite($site);

        $site->load('sslCertificates');

        if ($activeSsl) {
            $this->remoteTaskService->writeToFile(self::NGINX_SERVER_FILES . '/' . $site->domain . '/server/listen', '
            
gzip off; 
server_name ' . ($site->wildcard_domain ? '.' : '') . $site->domain . ';
listen 443 ssl http2 ' . ($site->domain == 'default' ? 'default_server' : null) . ';
listen [::]:443 ssl http2 ' . ($site->domain == 'default' ? 'default_server' : null) . ';

' . $location . '

ssl_certificate_key ' . ServerService::SSL_FILES . '/' . $activeSsl->id . '/server.key;
ssl_certificate ' . ServerService::SSL_FILES . '/' . $activeSsl->id . '/server.crt;

ssl_session_timeout 1d;
ssl_session_cache shared:SSL:50m;
ssl_session_tickets off;

ssl_dhparam /etc/nginx/dhparam.pem;

ssl_ecdh_curve secp384r1;

ssl_protocols TLSv1.2;
ssl_ciphers "EECDH+AESGCM:EDH+AESGCM:AES256+EECDH:AES256+EDH";
ssl_prefer_server_ciphers on;

ssl_stapling on;
ssl_stapling_verify on;

resolver 8.8.8.8 8.8.4.4 valid=300s;
resolver_timeout 10s;

add_header X-Frame-Options DENY;
add_header X-Content-Type-Options nosniff;

');

            $this->remoteTaskService->writeToFile(self::NGINX_SERVER_FILES . '/' . $site->domain . '/before/ssl_redirect.conf', '
server {
    listen 80 ' . ($site->domain == 'default' ? 'default_server' : null) . ';
    listen [::]:80 ' . ($site->domain == 'default' ? 'default_server' : null) . ';
    
    server_name ' . ($site->wildcard_domain ? '.' : '') . $site->domain . ';
    return 301 https://$host$request_uri;
}
');
        } else {
            $this->remoteTaskService->writeToFile(self::NGINX_SERVER_FILES . '/' . $site->domain . '/server/listen', '
listen 80 ' . ($site->domain == 'default' ? 'default_server' : null) . ';
listen [::]:80 ' . ($site->domain == 'default' ? 'default_server' : null) . ';
server_name ' . ($site->wildcard_domain ? '.' : '') . $site->domain . ';
add_header Strict-Transport-Security max-age=0;

' . $location . '

');

            $this->remoteTaskService->removeFile(self::NGINX_SERVER_FILES.'/'.$site->domain.'/before/ssl_redirect.conf');
        }
    }

    private function createWebServerSite($site)
    {
        $this->connectToServer();

        $domain = $site->domain;

        $webserver = $this->getWebServer();

        $config = '';

        switch ($webserver) {
            case 'Nginx':

                $headers = '
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";
';

                if ($this->server->type !== SystemService::LOAD_BALANCER) {
                    if ($site->isLoadBalanced()) {
                        $headers = '';
                    }
                    $config = create_system_service('Languages\\'.$site->type.'\\'.$site->type, $this->server)->getNginxConfig($site);
                }

                return $this->remoteTaskService->writeToFile('/etc/nginx/sites-enabled/' . $domain, '
# codepier CONFIG (DO NOT REMOVE!)
include ' . self::NGINX_SERVER_FILES . '/' . $domain . '/before/*;

server {
    include ' . self::NGINX_SERVER_FILES . '/' . $domain . '/server/*;

    charset utf-8;
    
    ' . $headers . '
        
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    location /.well-known/acme-challenge {
        alias /home/codepier/.well-known/acme-challenge;
    }
    
    sendfile off;
    
    ' . $config . '
    
    location ~ /\.ht {
        deny all;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    access_log off;
    error_log  /var/log/nginx/' . $domain . '-error.log error;
}

# codepier CONFIG (DO NOT REMOVE!)
include ' . self::NGINX_SERVER_FILES . '/' . $domain . '/after/*;
');
                break;
        }
    }

    /**
     * @param Site   $site
     *
     * @return bool
     */
    public function createWebServerConfig(Site $site)
    {
        $this->connectToServer();

        $this->remoteTaskService->makeDirectory(self::NGINX_SERVER_FILES."/$site->domain/before");
        $this->remoteTaskService->makeDirectory(self::NGINX_SERVER_FILES."/$site->domain/server");
        $this->remoteTaskService->makeDirectory(self::NGINX_SERVER_FILES."/$site->domain/after");
        $this->remoteTaskService->makeDirectory(self::NGINX_SERVER_FILES."/$site->domain/root-location");

        $this->updateWebServerConfig($site, $this->server->type);
    }

    /**
     * @param Site $site
     */
    public function removeWebServerConfig(Site $site)
    {
        $this->connectToServer();

        $this->remoteTaskService->removeDirectory("/etc/nginx/sites-enabled/$site->domain");
        $this->remoteTaskService->removeDirectory(self::NGINX_SERVER_FILES."/$site->domain");
    }

    private function getWebServer()
    {
        $webServiceFeatures = $this->server->server_features['WebService'];

        if (isset($webServiceFeatures['Nginx']['enabled']) && isset($webServiceFeatures['Nginx']['enabled']) == 1) {
            return 'Nginx';
        }

        throw new \Exception('we dont have apache setup yet');
    }
}
