<?php

namespace App\Services\Systems\WebServers;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Models\Site\SiteSslCertificate;
use App\Services\Site\SiteService;

/**
 * // TODO - https://galaxy.ansible.com/  - https://github.com/codepier
 */
class NginxWebServerService implements WebServerContract
{
    protected $sslFilesPath;
    protected $serverService;
    protected $remoteTaskService;
    protected $repositoryService;

    const WEB_SERVER_FILES = '/etc/nginx/codepier-conf';
    const SSL_FILES = '/etc/opt/ssl';
    const LETS_ENCRYPT = 'Let\'s Encrypt';

    /**
     * SiteService constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     * @param \App\Services\Repository\RepositoryService | RepositoryService $repositoryService
     */
    public function __construct(
        ServerService $serverService,
        RemoteTaskService $remoteTaskService,
        RepositoryService $repositoryService
    ) {
        $this->serverService = $serverService;
        $this->remoteTaskService = $remoteTaskService;
        $this->repositoryService = $repositoryService;

        $this->sslFilesPath = self::SSL_FILES;
    }

    /**
     * @param Server $server
     * @param Site   $site
     *
     * @return bool
     */
    public function create(Server $server, Site $site)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->makeDirectory(self::WEB_SERVER_FILES."/$site->domain/before");
        $this->remoteTaskService->makeDirectory(self::WEB_SERVER_FILES."/$site->domain/server");
        $this->remoteTaskService->makeDirectory(self::WEB_SERVER_FILES."/$site->domain/after");

        $this->createWebServerSite($site->domain);
        $this->updateWebServerConfig($server, $site);
    }

    public function remove(Site $site)
    {
        $this->remoteTaskService->removeDirectory("/etc/nginx/sites-enabled/$site->domain");
        $this->remoteTaskService->removeDirectory(self::WEB_SERVER_FILES."/$site->domain");
    }

    public function removeSslFiles(Site $site)
    {
        $this->remoteTaskService->removeFile(self::WEB_SERVER_FILES."/$site->domain/before/ssl_redirect.conf");
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site $site
     */
    public function updateWebServerConfig(Server $server, Site $site)
    {
        $site->load('activeSSL');

        $this->remoteTaskService->ssh($server);

        if ($site->hasActiveSSL()) {
            $this->remoteTaskService->writeToFile(self::WEB_SERVER_FILES.'/'.$site->domain.'/server/listen', '
server_name '.($site->wildcard_domain ? '.' : '').$site->domain.';
listen 443 ssl http2 '.($site->domain == 'default' ? 'default_server' : null).';
listen [::]:443 ssl http2 '.($site->domain == 'default' ? 'default_server' : null).';

root /home/codepier/'.$site->domain.($site->zerotime_deployment ? '/current' : null).'/'.$site->web_directory.';


ssl_certificate_key '.$this->sslFilesPath.'/'.$site->domain.'/'.$site->activeSSL->id.'/server.key;
ssl_certificate '.$this->sslFilesPath.'/'.$site->domain.'/'.$site->activeSSL->id.'/server.crt;

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

            $this->remoteTaskService->writeToFile(self::WEB_SERVER_FILES.'/'.$site->domain.'/before/ssl_redirect.conf', '
server {
    listen 80 '.($site->domain == 'default' ? 'default_server' : null).';
    listen [::]:80 '.($site->domain == 'default' ? 'default_server' : null).';
    return 301 https://$host$request_uri;
}
');
        } else {
            $this->remoteTaskService->writeToFile(self::WEB_SERVER_FILES.'/'.$site->domain.'/server/listen', '
server_name '.($site->wildcard_domain ? '.' : '').$site->domain.';
listen 80 '.($site->domain == 'default' ? 'default_server' : null).';
listen [::]:80 '.($site->domain == 'default' ? 'default_server' : null).';

root /home/codepier/'.$site->domain.($site->zerotime_deployment ? '/current' : null).'/'.$site->web_directory.';
');
        }
    }

    /**
     * @param $domain
     *
     * @return bool
     */
    private function createWebServerSite($domain)
    {
        return $this->remoteTaskService->writeToFile('/etc/nginx/sites-enabled/'.$domain, '
# codepier CONFIG (DO NOT REMOVE!)
include '.self::WEB_SERVER_FILES.'/'.$domain.'/before/*;

server {
    include '.self::WEB_SERVER_FILES.'/'.$domain.'/server/*;

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location /.well-known/acme-challenge {
        alias /home/codepier/.well-known/acme-challenge;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    access_log off;
    error_log  /var/log/nginx/'.$domain.'-error.log error;

    sendfile off;

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

# codepier CONFIG (DO NOT REMOVE!)
include '.self::WEB_SERVER_FILES.'/'.$domain.'/after/*;
');
    }

    /**
     * @param \App\Models\Server\Server             $server
     * @param SiteSslCertificate $siteSslCertificate
     *
     * @return array
     */
    public function installSSL(Server $server, SiteSslCertificate $siteSslCertificate)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run(
            'letsencrypt certonly --non-interactive --agree-tos --email '.$server->user->email.' --webroot -w /home/codepier/ --expand -d '.implode(' -d', explode(',', $siteSslCertificate->domains))
        );

        if (count($errors = $this->remoteTaskService->getErrors())) {
            return $errors;
        }

        $this->remoteTaskService->run('crontab -l | (grep letsencrypt) || ((crontab -l; echo "* */12 * * * letsencrypt renew >/dev/null 2>&1") | crontab)');

        $this->activateSSL($server, $siteSslCertificate);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site   $site
     * @param $key
     * @param $cert
     *
     * @return array
     */
    public function installExistingSSL(Server $server, Site $site, $key, $cert)
    {
        $this->remoteTaskService->ssh($server);

        if ($site->hasActiveSSL()) {
            $activeSSL = $site->activeSSL;
            $activeSSL->active = false;
            $activeSSL->save();
        }

        $siteSLL = SiteSslCertificate::create([
            'site_id' => $site->id,
            'domains' => null,
            'type'    => self::LETS_ENCRYPT,
            'active'  => true,
        ]);

        $sslCertPath = self::SSL_FILES.'/'.$site->domain.'/'.$siteSLL->id;

        $siteSLL->key_path = $sslCertPath.'/server.key';
        $siteSLL->cert_path = $sslCertPath.'/server.crt';
        $siteSLL->save();


        $this->remoteTaskService->writeToFile($siteSLL->key_path, $key);
        $this->remoteTaskService->writeToFile($siteSLL->cert_path, $cert);

        $this->updateWebServerConfig($server, $site);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site   $site
     */
    public function mapSSL(Server $server, Site $site)
    {
        $this->remoteTaskService->ssh($server);

        $activeSSL = $site->load('activeSSL')->activeSSL;

        $sslCertPath = self::SSL_FILES.'/'.$site->domain.'/'.$activeSSL->id;

        $this->remoteTaskService->makeDirectory($sslCertPath);

        $this->remoteTaskService->run("ln -f -s $activeSSL->key_path $sslCertPath/server.key");
        $this->remoteTaskService->run("ln -f -s $activeSSL->cert_path $sslCertPath/server.crt");
    }

    /**
     * @param Server $server
     * @param \App\Models\Site\Site   $site
     *
     * @return array
     */
    public function deactivateSSL(Server $server, Site $site)
    {
        $this->remoteTaskService->ssh($server);

        $site->activeSSL->active = false;
        $site->activeSSL->save();

        $this->updateWebServerConfig($server, $site);

        $this->getWebServerService()->removeSslFiles($site);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param SiteSslCertificate $siteSslCertificate
     */
    public function removeSSL(Server $server, SiteSslCertificate $siteSslCertificate)
    {
        $this->remoteTaskService->ssh($server);

        switch ($siteSslCertificate->type) {
            case self::LETS_ENCRYPT:
                // leave it be we don't want to erase them cause they aren't unique
                break;
            default:
                $this->remoteTaskService->removeFile($siteSslCertificate->key_path);
                $this->remoteTaskService->removeFile($siteSslCertificate->cert_path);
                break;
        }

        $siteSslCertificate->delete();
    }

    /**
     * @param \App\Models\Server\Server             $server
     * @param \App\Models\Site\SiteSslCertificate $siteSslCertificate
     */
    public function activateSSL(Server $server, SiteSslCertificate $siteSslCertificate)
    {
        $site = $siteSslCertificate->site;

        if ($site->hasActiveSSL()) {
            $site->activeSSL->active = false;
            $site->activeSSL->save();
        }

        $siteSslCertificate->active = true;
        $siteSslCertificate->save();

        $site->load('activeSSL');

        if ($siteSslCertificate->type == self::LETS_ENCRYPT) {
            $this->mapSSL($server, $site);
        }

        $this->updateWebServerConfig($server, $site);
    }

    public function checkSSL()
    {
        //        openssl x509 -in /etc/letsencrypt/live/codepier.io/cert.pem -noout -enddate
    }
}
