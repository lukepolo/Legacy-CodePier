<?php

namespace App\Services\Server\Site;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\Site\SiteServiceContract;
use App\Models\Server;
use App\Models\Site;
use App\Models\SiteSSLCertificate;

/**
 * Class SiteService
 * @package App\Services
 */
class SiteService implements SiteServiceContract
{
    protected $remoteTaskService;

    public $deploymentServices = [
        'php' => DeploymentServices\PHP::class
    ];

    /**
     * SiteService constructor.
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     */
    public function __construct(RemoteTaskService $remoteTaskService)
    {
        $this->remoteTaskService = $remoteTaskService;
    }

    /**
     * Creates a site on the server
     * @param Server $server
     * @param string $domain
     * @param $wildCardDomain
     * @return bool
     */
    public function create(Server $server, $domain = 'default', $wildCardDomain)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('mkdir -p /etc/nginx/codepier-conf/' . $domain . '/before');
        $this->remoteTaskService->run('mkdir -p /etc/nginx/codepier-conf/' . $domain . '/server');

        $this->remoteTaskService->writeToFile('/etc/nginx/codepier-conf/' . $domain . '/server/listen', '
listen 80' . $domain == 'default' ? 'default_server' : null . ';
listen [::]:80' . $domain == 'default' ? 'default_server' : null . ';
');

        $this->remoteTaskService->run('mkdir -p /etc/nginx/codepier-conf/' . $domain . '/after');

        if (
        $this->remoteTaskService->writeToFile('/etc/nginx/sites-enabled/' . $domain, '
# codepier CONFIG (DOT NOT REMOVE!)
include codepier-conf/' . $domain . '/before/*;

server {
    include codepier-conf/' . $domain . '/server/*;
    
    server_name ' . $wildCardDomain ? '.' : '' . $domain . ';
    root /home/codepier/' . $domain . '/current/public;

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
    error_log  /var/log/nginx/' . $domain . '-error.log error;

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

# codepier CONFIG (DOT NOT REMOVE!)
include codepier-conf/' . $domain . '/after/*;
')
        ) {

            $this->remoteTaskService->run('service nginx restart');

            return true;
        }

        return false;
    }

    /**
     * Renames a domain
     * @param Site $site
     * @param $domain
     * @throws \App\Exceptions\SshConnectionFailed
     */
    public function renameDomain(Site $site, $domain)
    {
        $this->remoteTaskService->ssh($site->server);

        $this->remoteTaskService->run('mv ' . $site->path . ' /home/codepier/' . $domain);

        $this->remove($site);
        $this->create($site->server, $domain);

        $site->domain = $domain;
        $site->path = '/home/codepier/' . $domain;

        $site->save();
    }

    /**
     * Updates the environment file for the site
     * @param Site $site
     * @param $env
     * @throws \App\Exceptions\SshConnectionFailed
     */
    public function updateEnv(Site $site, $env)
    {
        $this->remoteTaskService->ssh($site->server);

        $this->remoteTaskService->writeToFile('/home/codepier/' . $site->domain . '/.env ', $env);
    }

    /**
     * Removes a site from the server
     * @param Site $site
     * @throws \App\Exceptions\SshConnectionFailed
     */
    public function remove(Site $site)
    {
        $this->remoteTaskService->ssh($site->server);

        $this->remoteTaskService->run('rm /etc/nginx/sites-enabled/' . $site->domain);
        $this->remoteTaskService->run('rm /etc/nginx/codepier-conf/' . $site->domain . ' -rf');

    }

    /**
     * Installs an ssl certificate for the site
     * @param Site $site
     * @return array
     * @throws \App\Exceptions\SshConnectionFailed
     */
    public function installSSL(Site $site, $domains)
    {
        $this->remoteTaskService->ssh($site->server);

        $this->remoteTaskService->run('letsencrypt certonly --non-interactive --agree-tos --email ' . $site->server->user->email . ' --webroot -w /home/codepier/ -d ' . implode(' -d',
                explode(',', $domains)));

        $this->remoteTaskService->run('crontab -l | (grep letsencrypt && echo "found") || ((crontab -l; echo "* */12 * * * letsencrypt renew >/dev/null 2>&1") | crontab)');

        if (count($errors = $this->remoteTaskService->getErrors())) {
            return $errors;
        }

        SiteSSLCertificate::create([
            'site_id' => $site->id,
            'domains' => $domains,
            'type' => 'Let\'s Encrypt'
        ]);

        $this->remoteTaskService->writeToFile('/etc/nginx/codepier-conf/' . $site->domain . '/server/listen', '
listen 443 ssl http2 ' . $site->domain == 'default' ? 'default_server' : null . ';
listen [::]:443 ssl http2 ' . $site->domain == 'default' ? 'default_server' : null . ';

ssl_certificate /etc/letsencrypt/live/' . $site->domain . '/cert.pem;
ssl_certificate_key /etc/letsencrypt/live/codepier.io/privkey.pem;
ssl_trusted_certificate /etc/letsencrypt/live/codepier.io/fullchain.pem;

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

        $this->remoteTaskService->writeToFile('/etc/nginx/codepier-conf/' . $site->domain . '/before/ssl_redirect.conf', '
server {
    listen 80 ' . $site->domain == 'default' ? 'default_server' : null . ';
    listen [::]:80 ' . $site->domain == 'default' ? 'default_server' : null . ';
    server_name ' . $site->wildcard_domain ? '.' : '' . $site->domain . ';
    return 301 https://$host$request_uri;
}
');

        $this->remoteTaskService->run('service nginx restart');
    }

    /**
     * Removes a ssl cert from a site
     * @param Site $site
     * @throws \App\Exceptions\SshConnectionFailed
     */
    public function removeSSL(Site $site)
    {
        $this->remoteTaskService->ssh($site->server);

        $this->remoteTaskService->writeToFile('/etc/nginx/codepier-conf/' . $site->domain . '/server/listen', '
listen 80 ' . $site->domain == 'default' ? 'default_server' : null . ';
listen [::]:80 ' . $site->domain == 'default' ? 'default_server' : null . ';
');

        $this->remoteTaskService->run('rm /etc/nginx/codepier-conf/' . $site->domain . '/before/redirect');

        $this->remoteTaskService->run('service nginx restart');
    }

    /**
     * Deploys a site on the server
     * @param Server $server
     * @param Site $site
     * @param bool $zeroDownTime
     * @return bool
     */
    public function deploy(Server $server, Site $site, $zeroDownTime = true)
    {
        $deploymentService = $this->getDeploymentService($server, $site);
        $deploymentService->updateRepository();
        $deploymentService->installVendorPackages();
        $deploymentService->runMigrations();
        $deploymentService->setupFolders();
        $deploymentService->cleanup();

        $this->remoteTaskService->ssh($server);
        $this->remoteTaskService->run('service nginx restart');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * Gest the deployment service to execute the commands
     * @param Server $server
     * @param Site $site
     * @return mixed
     */
    private function getDeploymentService(Server $server, Site $site)
    {
        $deploymentService = 'php';
        return new $this->deploymentServices[$deploymentService]($this->remoteTaskService, $server, $site);
    }
}