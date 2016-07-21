<?php

namespace App\Services\Server\Site;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\Site\SiteServiceContract;
use App\Models\Server;
use App\Models\Site;
use App\Models\SiteDaemon;
use App\Models\SiteSslCertificate;

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
     * @param bool $wildCardDomain
     * @param bool $zerotimeDeployment
     * @param null $webDirectory
     * @return bool
     */
    public function create(Server $server, $domain = 'default', $wildCardDomain = false, $zerotimeDeployment = true, $webDirectory = null)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('mkdir -p /etc/nginx/codepier-conf/' . $domain . '/before');
        $this->remoteTaskService->run('mkdir -p /etc/nginx/codepier-conf/' . $domain . '/server');

        $this->remoteTaskService->writeToFile('/etc/nginx/codepier-conf/' . $domain . '/server/listen', '
server_name ' . ($wildCardDomain ? '.' : '') . $domain . ';
listen 80 ' . ($domain == 'default' ? 'default_server' : null) . ';
listen [::]:80 ' . ($domain == 'default' ? 'default_server' : null) . ';

root /home/codepier/' . $domain . ($zerotimeDeployment ? '/current' : null) . $webDirectory.';
');

        $this->remoteTaskService->run('mkdir -p /etc/nginx/codepier-conf/' . $domain . '/after');

        if (
        $this->remoteTaskService->writeToFile('/etc/nginx/sites-enabled/' . $domain, '
# codepier CONFIG (DO NOT REMOVE!)
include codepier-conf/' . $domain . '/before/*;

server {
    include codepier-conf/' . $domain . '/server/*;

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

        $this->remoteTaskService->run('mv /home/codepier/' . $site->domain . ' /home/codepier/' . $domain);

        $this->remove($site);

        $this->create($site->server, $domain, $site->wildcard_domain, $site->zerotime_deployment, $site->web_directory);

        $site->domain = $domain;

        $site->save();
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

        $this->remoteTaskService->run(
            'letsencrypt certonly --non-interactive --agree-tos --email ' . $site->server->user->email . ' --webroot -w /home/codepier/ --expand -d ' . implode(' -d',
                explode(',', $domains))
        );

        if (count($errors = $this->remoteTaskService->getErrors())) {
            return $errors;
        }

        $this->remoteTaskService->run('crontab -l | (grep letsencrypt && echo "found") || ((crontab -l; echo "* */12 * * * letsencrypt renew >/dev/null 2>&1") | crontab)');

        $siteSSL = SiteSslCertificate::firstOrCreate([
            'site_id' => $site->id,
        ]);

        $siteSSL->fill([
            'domains' => $domains,
            'type' => 'Let\'s Encrypt'
        ]);

        $siteSSL->save();

        $this->remoteTaskService->writeToFile('/etc/nginx/codepier-conf/' . $site->domain . '/server/listen', '
server_name ' . ($site->wildcard_domain ? '.' : '') . $site->domain . ';
listen 443 ssl http2 ' . ($site->domain == 'default' ? 'default_server' : null) . ';
listen [::]:443 ssl http2 ' . ($site->domain == 'default' ? 'default_server' : null) . ';

root /home/codepier/' . $site->domain . ($site->zerotime_deployment ? '/current' : null) . $site->web_directory.';

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
    listen 80 ' . ($site->domain == 'default' ? 'default_server' : null) . ';
    listen [::]:80 ' . ($site->domain == 'default' ? 'default_server' : null) . ';
    return 301 https://$host$request_uri;
}
');

        if ($site->hasSSL()) {
            $site->ssl->delete();
        }
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
server_name ' . ($site->wildcard_domain ? '.' : '') . $site->domain . ';
listen 80 ' . ($site->domain == 'default' ? 'default_server' : null) . ';
listen [::]:80 ' . ($site->domain == 'default' ? 'default_server' : null) . ';

root /home/codepier/' . $site->domain . ($site->zerotime_deployment ? '/current' : null) . $site->web_directory.';
');

        $this->remoteTaskService->run('rm /etc/nginx/codepier-conf/' . $site->domain . '/before/ssl_redirect.conf');

        $this->remoteTaskService->run('service nginx restart');

        $site->ssl->delete();
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

    /**
     * Installs a daemon
     * @param Site $site
     * @param $command
     * @param $autoStart
     * @param $autoRestart
     * @param $user
     * @param $numberOfWorkers
     */
    public function installDaemon(Site $site, $command, $autoStart, $autoRestart, $user, $numberOfWorkers)
    {
        $serverDaemon = SiteDaemon::create([
            'site_id' => $site->id,
            'command' => $command,
            'auto_start' => $autoStart,
            'auto_restart' => $autoRestart,
            'user' => $user,
            'number_of_workers' => $numberOfWorkers,
        ]);

        $this->remoteTaskService->ssh($site->server);

        $this->remoteTaskService->writeToFile('/etc/supervisor/conf.d/site-worker-' . $serverDaemon->id . '.conf ', '
[program:site-worker-' . $serverDaemon->id . ']
process_name=%(program_name)s_%(process_num)02d
command=' . $command . '
autostart=' . $autoStart . '
autorestart=' . $autoRestart . '
user=' . $user . '
numprocs=' . $numberOfWorkers . '
redirect_stderr=true
stdout_logfile=/home/codepier/workers/site-worker-' . $serverDaemon->id . '.log
');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');
        $this->remoteTaskService->run('supervisorctl start site-worker-' . $serverDaemon->id . ':*');

    }

    /**
     * Removes a daemon
     * @param Server $server
     * @param SiteDaemon $siteDaemon
     */
    public function removeDaemon(Server $server, SiteDaemon $siteDaemon)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('rm /etc/supervisor/conf.d/site-worker-' . $siteDaemon->id . '.conf');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');


        $siteDaemon->delete();
    }

    public function deleteSite(Site $site)
    {
        foreach($site->daemons as $daemon) {
            $this->removeDaemon($site->server, $daemon);
        }

        $this->remove($site);

        $site->delete();
    }


    /*
     * TODO - needs to be customized
     */
    public function updateMaxUploadSize(Site $site, $megabytes)
    {
        $this->remoteTaskService->ssh($site->server);

        $this->remoteTaskService->writeToFile('/etc/nginx/conf.d/uploads.conf', 'client_max_body_size '. $megabytes.'M;');
        $this->remoteTaskService->run('sed -i "s/upload_max_filesize = .*/upload_max_filesize = '.$megabytes.'M/" /etc/php/7.0/fpm/php.ini');
        $this->remoteTaskService->run('sed -i "s/post_max_size = .*/post_max_size = '.$megabytes.'M/" /etc/php/7.0/fpm/php.ini');

        $this->remoteTaskService->run('service nginx restart');
        $this->remoteTaskService->run('service php7.0-fpm restart');
    }
}