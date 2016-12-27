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
    }

    /**
     * @description NGINX is a free, open-source, high-performance HTTP server and reverse proxy, as well as an IMAP/POP3 proxy server.
     */
    public function installNginx($workerProcesses = 1, $workerConnections = 512)
    {
        $this->connectToServer();

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

        $this->remoteTaskService->run('mkdir -p /etc/nginx/codepier-conf');

        $this->remoteTaskService->writeToFile('/etc/nginx/dhparam.pem', env('DH_PARAM'));

        $this->remoteTaskService->appendTextToFile('/etc/nginx/fastcgi_params', 'fastcgi_param HTTP_PROXY "";');

        $this->remoteTaskService->run('service nginx stop');

        \Log::info($this->remoteTaskService->run('nginx -c /etc/nginx/nginx.conf', true));

        $this->remoteTaskService->run('service nginx restart');

        $this->remoteTaskService->writeToFile('/etc/nginx/dhparam.pem',
            '-----BEGIN DH PARAMETERS-----
MIIBCAKCAQEA5M2MrvvA978Z4Zz6FBf/1CUZA3QcJyCUmeMwPVWBeTS9M3XJTYUY
Hr7UXZQtzWF5o3GLC2SAMzVVHGaJQDnruxBT5HLsneFpSZz5ntCq4tLLIE32dyYd
Vd/K+Mp1Cee3lw57iK/ZC/CfxoZ5qtWJ9/CRmfXwS8QMwmLl+pR8v5m0I4TqzgRM
1HEbY1YvgKNiy24HbOhr62Von27Fa8IpGVVhLjoL6VTNaGjh64vtbMZzp1Va9G5P
rPJFzPmaWrfBecGIEWEN77NLT8ieYpiLUw0s4PgnlM6Pijax/Z/YsqsZpN8nvmDc
gQw5FUmzayuEHRxRIy1uQ6qkPRThOrGQswIBAg==
-----END DH PARAMETERS-----');

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, 'service nginx restart');
    }

    /**
     * @param Site $site
     */
    public function updateWebServerConfig(Site $site)
    {
        $this->connectToServer();

        $site->load('activeSSL');

        $this->remoteTaskService->ssh($this->server);

        if ($site->hasActiveSSL()) {
            $activeSsl = $this->server->activeSslCertificates->first(function ($sslCert) use ($site) {
                return $site->activeSSL->id == $sslCert->site_ssl_certificate_id;
            });

            $this->remoteTaskService->writeToFile(self::NGINX_SERVER_FILES.'/'.$site->domain.'/server/listen', '
server_name '.($site->wildcard_domain ? '.' : '').$site->domain.';
listen 443 ssl http2 '.($site->domain == 'default' ? 'default_server' : null).';
listen [::]:443 ssl http2 '.($site->domain == 'default' ? 'default_server' : null).';

root /home/codepier/'.$site->domain.($site->zerotime_deployment ? '/current' : null).'/'.$site->web_directory.';


ssl_certificate_key '.ServerService::SSL_FILES.'/'.$site->domain.'/'.$activeSsl->id.'/server.key;
ssl_certificate '.ServerService::SSL_FILES.'/'.$site->domain.'/'.$activeSsl->id.'/server.crt;

ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
ssl_prefer_server_ciphers on;
ssl_dhparam /etc/nginx/dhparam.pem;
ssl_ciphers \'ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-AES256-GCM-SHA384:DHE-RSA-AES128-GCM-SHA256:DHE-DSS-AES128-GCM-SHA256:kEDH+AESGCM:ECDHE-RSA-AES128-SHA256:ECDHE-ECDSA-AES128-SHA256:ECDHE-RSA-AES128-SHA:ECDHE-ECDSA-AES128-SHA:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA:ECDHE-ECDSA-AES256-SHA:DHE-RSA-AES128-SHA256:DHE-RSA-AES128-SHA:DHE-DSS-AES128-SHA256:DHE-RSA-AES256-SHA256:DHE-DSS-AES256-SHA:DHE-RSA-AES256-SHA:AES128-GCM-SHA256:AES256-GCM-SHA384:AES128-SHA256:AES256-SHA256:AES128-SHA:AES256-SHA:AES:CAMELLIA:DES-CBC3-SHA:!aNULL:!eNULL:!EXPORT:!DES:!RC4:!MD5:!PSK:!aECDH:!EDH-DSS-DES-CBC3-SHA:!EDH-RSA-DES-CBC3-SHA:!KRB5-DES-CBC3-SHA\';
ssl_session_timeout 1d;
ssl_session_cache shared:SSL:50m;
ssl_stapling on;
ssl_stapling_verify on;
add_header Strict-Transport-Security max-age=15768000;
');

            // TODO - breaks with multiple
            // listen [::]:80 '.($site->domain == 'default' ? 'default_server' : null).' ipv6only=on;
            $this->remoteTaskService->writeToFile(self::NGINX_SERVER_FILES.'/'.$site->domain.'/before/ssl_redirect.conf', '
server {
    listen 80 '.($site->domain == 'default' ? 'default_server' : null).';
    
    server_name '.($site->wildcard_domain ? '.' : '').$site->domain.';
    return 301 https://$host$request_uri;
}
');
        } else {
            // TODO - breaks with multiple
            // listen [::]:80 '.($site->domain == 'default' ? 'default_server' : null).' ipv6only=on;
            $this->remoteTaskService->writeToFile(self::NGINX_SERVER_FILES.'/'.$site->domain.'/server/listen', '
listen 80 '.($site->domain == 'default' ? 'default_server' : null).';
server_name '.($site->wildcard_domain ? '.' : '').$site->domain.';

root /home/codepier/'.$site->domain.($site->zerotime_deployment ? '/current' : null).'/'.$site->web_directory.';
');

            $this->remoteTaskService->removeFile(self::NGINX_SERVER_FILES.'/'.$site->domain.'/before/ssl_redirect.conf');
        }
    }

    private function createWebServerSite($site)
    {
        $domain = $site->domain;
        $this->connectToServer();

        $webserver = $this->getWebServer();

        switch ($webserver) {
            case 'Nginx':
                $config = create_system_service('Languages\\'.$site->type.'\\'.$site->type, $this->server)->getNginxConfig($site);

                return $this->remoteTaskService->writeToFile('/etc/nginx/sites-enabled/'.$domain, '
# codepier CONFIG (DO NOT REMOVE!)
include '.self::NGINX_SERVER_FILES.'/'.$domain.'/before/*;

server {
    include '.self::NGINX_SERVER_FILES.'/'.$domain.'/server/*;

    charset utf-8;
    
    location /.well-known/acme-challenge {
        alias /home/codepier/.well-known/acme-challenge;
    }
    
    access_log off;
    error_log  /var/log/nginx/'.$domain.'-error.log error;
    
    sendfile off;
    
    location ~ /\.ht {
        deny all;
    }
    '.$config.'
}

# codepier CONFIG (DO NOT REMOVE!)
include '.self::NGINX_SERVER_FILES.'/'.$domain.'/after/*;
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

        $this->createWebServerSite($site);
        $this->updateWebServerConfig($site);
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

        dd('we dont have apache setup yet');
    }
}
