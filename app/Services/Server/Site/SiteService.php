<?php

namespace App\Services\Server\Site;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\Site\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Server\Site\SiteServiceContract;
use App\Events\Server\Site\DeploymentCompleted;
use App\Events\Server\Site\DeploymentStepCompleted;
use App\Events\Server\Site\DeploymentStepFailed;
use App\Events\Server\Site\DeploymentStepStarted;
use App\Exceptions\DeploymentFailed;
use App\Exceptions\FailedCommand;
use App\Models\Server;
use App\Models\Site;
use App\Models\SiteDaemon;
use App\Models\SiteDeployment;
use App\Models\SiteSslCertificate;
use App\Services\Server\Site\DeploymentServices\PHP;

/**
 * Class SiteService
 * @package App\Services
 */
class SiteService implements SiteServiceContract
{
    protected $remoteTaskService;
    protected $repositoryService;

    public $deploymentServices = [
        'php' => DeploymentServices\PHP::class
    ];

    /**
     * SiteService constructor.
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     * @param \App\Services\Server\Site\Repository\RepositoryService | RepositoryService $repositoryService
     */
    public function __construct(RemoteTaskService $remoteTaskService, RepositoryService $repositoryService)
    {
        $this->remoteTaskService = $remoteTaskService;
        $this->repositoryService = $repositoryService;
    }

    /**
     * @param Server $server
     * @param Site $site
     * @return bool
     */
    public function create(Server $server, Site $site)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->makeDirectory("/etc/nginx/codepier-conf/$site->domain/before");
        $this->remoteTaskService->makeDirectory("/etc/nginx/codepier-conf/$site->domain/server");
        $this->remoteTaskService->makeDirectory("/etc/nginx/codepier-conf/$site->domain/after");

        if (empty($this->remoteTaskService->getErrors()) && empty($this->createNginxSite($site->domain))) {
            $this->updateSiteNginxConfig($site, "/etc/nginx/codepier-conf/$site->domain/server/listen");
            return $this->remoteTaskService->run('service nginx restart');
        }

        $this->remoteTaskService->ssh($server, 'codepier');

        $this->remoteTaskService->makeDirectory('/home/codepier/'.$site->domain);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Site $site
     * @param $domain
     * @return array
     * @throws \App\Exceptions\SshConnectionFailed
     */
    public function renameDomain(Site $site, $domain)
    {
        $this->remoteTaskService->ssh($site->server);

        $this->remoteTaskService->run('mv /home/codepier/' . $site->domain . ' /home/codepier/' . $domain);

        $this->remove($site);

        $this->create($site->server, $domain, $site->wildcard_domain, $site->zerotime_deployment, $site->web_directory);

        foreach ($site->daemons as $daemon) {
            $this->installDaemon(
                $site,
                str_replace($site->domain, $domain, $daemon->command),
                true,
                true,
                $daemon->user,
                1
            );

            $this->removeDaemon($site->server, $daemon);
        }

        $site->domain = $domain;

        $site->save();

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Site $site
     * @return array
     */
    public function remove(Site $site)
    {
        $this->remoteTaskService->ssh($site->server);

        $this->remoteTaskService->removeDirectory("/etc/nginx/sites-enabled/$site->domain");
        $this->remoteTaskService->removeDirectory("/etc/nginx/codepier-conf/$site->domain");

        return $this->remoteTaskService->getErrors();
    }


    /**
     * @param Site $site
     * @param $domains
     * @return array
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

        $this->remoteTaskService->run('crontab -l | (grep letsencrypt) || ((crontab -l; echo "* */12 * * * letsencrypt renew >/dev/null 2>&1") | crontab)');

        $siteSSL = SiteSslCertificate::firstOrCreate([
            'site_id' => $site->id,
        ]);

        $siteSSL->fill([
            'domains' => $domains,
            'type' => 'Let\'s Encrypt'
        ]);

        $siteSSL->save();

        $this->updateSiteNginxConfig($site);

        if ($site->hasSSL()) {
            $site->ssl->delete();
        }
        $this->remoteTaskService->run('service nginx restart');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Site $site
     * @return array
     */
    public function removeSSL(Site $site)
    {
        $this->remoteTaskService->ssh($site->server);

        $site->ssl->delete();

        $this->updateSiteNginxConfig($site);

        $this->remoteTaskService->removeFile("/etc/nginx/codepier-conf/$site->domain/before/ssl_redirect.conf");
        $this->remoteTaskService->run('service nginx restart');

        return $this->remoteTaskService->getErrors();
    }


    /**
     * @param Server $server
     * @param Site $site
     * @param SiteDeployment $siteDeployment
     * @param null $sha
     * @throws DeploymentFailed
     */
    public function deploy(Server $server, Site $site, SiteDeployment $siteDeployment, $sha = null)
    {
        $deploymentService = $this->getDeploymentService($server, $site);

        if(empty($lastCommit = $sha)) {
            $lastCommit = $this->repositoryService->getLatestCommit($site->userRepositoryProvider, $site->repository, $site->branch);
        }

        $siteDeployment->git_commit = $lastCommit;
        $siteDeployment->save();

        foreach ($siteDeployment->events as $event) {
            try {
                $start = microtime(true);

                event(new DeploymentStepStarted($site, $event));

                $internalFunction = $event->step->internal_deployment_function;

                $log = $deploymentService->$internalFunction($sha);

                event(new DeploymentStepCompleted($site, $event, $log, microtime(true) - $start));
            } catch(FailedCommand $e) {
                event(new DeploymentStepFailed($site, $event, $e->getMessage()));
                throw new DeploymentFailed($e->getMessage());
            }
        }

        $this->remoteTaskService->ssh($server);
        $this->remoteTaskService->run('service nginx restart');

        event(new DeploymentCompleted($site, $siteDeployment, 'Commit #####', $this->remoteTaskService->getOutput()));
    }


    /**
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
     * @param Site $site
     * @param $command
     * @param $autoStart
     * @param $autoRestart
     * @param $user
     * @param $numberOfWorkers
     * @return array
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

        return $this->remoteTaskService->getErrors();

    }


    /**
     * @param Server $server
     * @param SiteDaemon $siteDaemon
     * @return array|bool
     */
    public function removeDaemon(Server $server, SiteDaemon $siteDaemon)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->removeFile("/etc/supervisor/conf.d/site-worker-$siteDaemon->id.conf");

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');

        $errors = $this->remoteTaskService->getErrors();

        if (empty($errors)) {
            $siteDaemon->delete();
            return true;
        }

        return $errors;
    }

    /**
     * @param Site $site
     * @return array|bool
     */
    public function deleteSite(Site $site)
    {
        foreach ($site->daemons as $daemon) {
            $this->removeDaemon($site->server, $daemon);
        }

        $errors = $this->remoteTaskService->getErrors();

        if (empty($errors)) {
            $errors = $this->remove($site);
        }

        if (empty($errors)) {
            $site->delete();
            return true;
        }

        return $this->remoteTaskService->getErrors();
    }

    /*
     * TODO - needs to be customized
     */
    /**
     * @param Site $site
     * @param $megabytes
     * @return array
     */
    public function updateMaxUploadSize(Site $site, $megabytes)
    {
        $this->remoteTaskService->ssh($site->server);

        $this->remoteTaskService->writeToFile('/etc/nginx/conf.d/uploads.conf',
            'client_max_body_size ' . $megabytes . 'M;'
        );
        $this->remoteTaskService->removeLineByText('/etc/php/7.0/fpm/php.ini', 'upload_max_filesize = .*',
            'upload_max_filesize = ' . $megabytes . 'M');
        $this->remoteTaskService->removeLineByText('/etc/php/7.0/fpm/php.ini', 'post_max_size = .*',
            'post_max_size = ' . $megabytes . 'M');

        $this->remoteTaskService->run('service nginx restart');
        $this->remoteTaskService->run('service php7.0-fpm restart');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Site $site
     * @return array
     */
    public function updateSiteNginxConfig(Site $site)
    {
        $this->remoteTaskService->ssh($site->server);
        if ($site->hasSSL()) {

            $this->remoteTaskService->writeToFile('/etc/nginx/codepier-conf/' . $site->domain . '/server/listen', '
server_name ' . ($site->wildcard_domain ? '.' : '') . $site->domain . ';
listen 443 ssl http2 ' . ($site->domain == 'default' ? 'default_server' : null) . ';
listen [::]:443 ssl http2 ' . ($site->domain == 'default' ? 'default_server' : null) . ';

root /home/codepier/' . $site->domain . ($site->zerotime_deployment ? '/current' : null) . '/' . $site->web_directory . ';

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
        } else {
            $this->remoteTaskService->writeToFile('/etc/nginx/codepier-conf/' . $site->domain . '/server/listen', '
server_name ' . ($site->wildcard_domain ? '.' : '') . $site->domain . ';
listen 80 ' . ($site->domain == 'default' ? 'default_server' : null) . ';
listen [::]:80 ' . ($site->domain == 'default' ? 'default_server' : null) . ';

root /home/codepier/' . $site->domain . ($site->zerotime_deployment ? '/current' : null) . '/' . $site->web_directory . ';
');
        }

        $this->remoteTaskService->run('service nginx restart');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param $domain
     * @return bool
     */
    private function createNginxSite($domain)
    {
        return $this->remoteTaskService->writeToFile('/etc/nginx/sites-enabled/' . $domain, '
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
');
    }
}