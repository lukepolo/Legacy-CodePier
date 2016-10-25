<?php

namespace App\Services\Site;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Site\SiteServiceContract;
use App\Events\Site\DeploymentStepCompleted;
use App\Events\Site\DeploymentStepFailed;
use App\Events\Site\DeploymentStepStarted;
use App\Exceptions\DeploymentFailed;
use App\Exceptions\FailedCommand;
use App\Models\Server;
use App\Models\Site;
use App\Models\SiteWorker;
use App\Models\SiteDeployment;
use App\Models\SiteSslCertificate;
use App\Services\Site\DeploymentServices\PHP;

/**
 * Class SiteService.
 */
class SiteService implements SiteServiceContract
{
    protected $remoteTaskService;
    protected $repositoryService;

    const LETS_ENCRYPT = 'Let\'s Encrypt';

    public $deploymentServices = [
        'php' => DeploymentLanguages\PHP::class,
    ];

    /**
     * SiteService constructor.
     *
     * @param \App\Services\RemoteTaskService | RemoteTaskService                        $remoteTaskService
     * @param \App\Services\Repository\RepositoryService | RepositoryService $repositoryService
     */
    public function __construct(RemoteTaskService $remoteTaskService, RepositoryService $repositoryService)
    {
        $this->remoteTaskService = $remoteTaskService;
        $this->repositoryService = $repositoryService;
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

        $this->remoteTaskService->makeDirectory("/etc/nginx/codepier-conf/$site->domain/before");
        $this->remoteTaskService->makeDirectory("/etc/nginx/codepier-conf/$site->domain/server");
        $this->remoteTaskService->makeDirectory("/etc/nginx/codepier-conf/$site->domain/after");

        if (empty($this->remoteTaskService->getErrors())) {
            $this->createNginxSite($site->domain);
            $this->updateSiteNginxConfig($server, $site);

            return $this->remoteTaskService->run('service nginx restart');
        }

        $this->remoteTaskService->ssh($server, 'codepier');

        $this->remoteTaskService->makeDirectory('/home/codepier/'.$site->domain);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param Site   $site
     * @param $domain
     *
     * @return array
     */
    public function renameDomain(Server $server, Site $site, $domain)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('mv /home/codepier/'.$site->domain.' /home/codepier/'.$domain);

        $this->remove($site);

        $this->create($server, $domain, $site->wildcard_domain, $site->zerotime_deployment, $site->web_directory);

        // todo - fix
        dd('SITE SERVICE THIS NO LONGER APPLIES');
//        foreach ($site->workers as $worker) {
//            $this->installDaemon(
//                $site,
//                str_replace($site->domain, $domain, $worker->command),
//                true,
//                true,
//                $worker->user,
//                1
//            );
//
//            $this->removeDaemon($server, $daemon);
//        }

        $site->domain = $domain;

        $site->save();

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param Site   $site
     *
     * @return array
     */
    public function remove(Server $server, Site $site)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->removeDirectory("/etc/nginx/sites-enabled/$site->domain");
        $this->remoteTaskService->removeDirectory("/etc/nginx/codepier-conf/$site->domain");

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server             $server
     * @param SiteSslCertificate $siteSslCertificate
     *
     * @return array
     */
    public function installSSL(Server $server, SiteSslCertificate $siteSslCertificate)
    {
        /** @var Site $site */
        $site = $siteSslCertificate->site;
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->writeToFile('/etc/nginx/dhparam.pem',
"-----BEGIN DH PARAMETERS-----
MIIBCAKCAQEA5M2MrvvA978Z4Zz6FBf/1CUZA3QcJyCUmeMwPVWBeTS9M3XJTYUY
Hr7UXZQtzWF5o3GLC2SAMzVVHGaJQDnruxBT5HLsneFpSZz5ntCq4tLLIE32dyYd
Vd/K+Mp1Cee3lw57iK/ZC/CfxoZ5qtWJ9/CRmfXwS8QMwmLl+pR8v5m0I4TqzgRM
1HEbY1YvgKNiy24HbOhr62Von27Fa8IpGVVhLjoL6VTNaGjh64vtbMZzp1Va9G5P
rPJFzPmaWrfBecGIEWEN77NLT8ieYpiLUw0s4PgnlM6Pijax/Z/YsqsZpN8nvmDc
gQw5FUmzayuEHRxRIy1uQ6qkPRThOrGQswIBAg==
-----END DH PARAMETERS-----");

        $this->remoteTaskService->run(
            'letsencrypt certonly --non-interactive --agree-tos --email '.$server->user->email.' --webroot -w /home/codepier/ --expand -d '.implode(' -d', explode(',', $siteSslCertificate->domains))
        );

        if (count($errors = $this->remoteTaskService->getErrors())) {
            return $errors;
        }

        $this->remoteTaskService->run('crontab -l | (grep letsencrypt) || ((crontab -l; echo "* */12 * * * letsencrypt renew >/dev/null 2>&1") | crontab)');

        if ($site->hasActiveSSL()) {
            $activeSSL = $site->activeSSL;
            $activeSSL->active = false;
            $activeSSL->save();
        }

        $siteSslCertificate->active = true;
        $siteSslCertificate->save();

        $this->mapSSL($server, $site);

        $this->updateSiteNginxConfig($server, $site);

        $this->remoteTaskService->run('service nginx restart');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
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

        $sslCertPath = '/etc/nginx/ssl/'.$site->domain.'/'.$siteSLL->id;

        $siteSLL->key_path = $sslCertPath.'/server.key';
        $siteSLL->cert_path = $sslCertPath.'/server.crt';
        $siteSLL->save();


        $this->remoteTaskService->writeToFile($siteSLL->key_path, $key);
        $this->remoteTaskService->writeToFile($siteSLL->cert_path, $cert);

        $this->updateSiteNginxConfig($server, $site);

        $this->remoteTaskService->run('service nginx restart');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param Site   $site
     */
    public function mapSSL(Server $server, Site $site)
    {
        $this->remoteTaskService->ssh($server);

        $activeSSL = $site->load('activeSSL')->activeSSL;

        $sslCertPath = '/etc/nginx/ssl/'.$site->domain.'/'.$activeSSL->id;

        $this->remoteTaskService->makeDirectory($sslCertPath);

        $this->remoteTaskService->run("ln -f -s $activeSSL->key_path $sslCertPath/server.key");
        $this->remoteTaskService->run("ln -f -s $activeSSL->cert_path $sslCertPath/server.crt");
    }

    /**
     * @param Server $server
     * @param Site   $site
     *
     * @return array
     */
    public function deactivateSSL(Server $server, Site $site)
    {
        $this->remoteTaskService->ssh($server);

        $site->activeSSL->active = false;
        $site->activeSSL->save();

        $this->updateSiteNginxConfig($server, $site);

        $this->remoteTaskService->removeFile("/etc/nginx/codepier-conf/$site->domain/before/ssl_redirect.conf");
        $this->remoteTaskService->run('service nginx restart');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server             $server
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
     * @param Server             $server
     * @param SiteSslCertificate $siteSslCertificate
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

        $this->updateSiteNginxConfig($server, $site);
    }

    /**
     * @param Server         $server
     * @param Site           $site
     * @param SiteDeployment $siteDeployment
     * @param null           $sha
     *
     * @throws DeploymentFailed
     */
    public function deploy(Server $server, Site $site, SiteDeployment $siteDeployment, $sha = null)
    {
        $deploymentService = $this->getDeploymentService($server, $site);

        if (empty($lastCommit = $sha)) {
            $lastCommit = $this->repositoryService->getLatestCommit($site->userRepositoryProvider, $site->repository, $site->branch);
        }

        $siteDeployment->git_commit = $lastCommit;
        $siteDeployment->save();

        foreach ($siteDeployment->events as $event) {
            try {
                $start = microtime(true);

                event(new DeploymentStepStarted($site, $event, $event->step));

                $internalFunction = $event->step->internal_deployment_function;

                $log = $deploymentService->$internalFunction($sha);

                event(new DeploymentStepCompleted($site, $event, $event->step, $log, microtime(true) - $start));
            } catch (FailedCommand $e) {
                event(new DeploymentStepFailed($site, $event, $e->getMessage()));
                throw new DeploymentFailed($e->getMessage());
            }
        }

        $this->remoteTaskService->ssh($server);
        $this->remoteTaskService->run('service nginx restart');
        $this->remoteTaskService->run('service php7.0-fpm restart');

        // TODO - should be a notification
//        event(new DeploymentCompleted($site, $siteDeployment, 'Commit #####', $this->remoteTaskService->getOutput()));
    }

    /**
     * @param Server $server
     * @param Site   $site
     *
     * @return mixed
     */
    private function getDeploymentService(Server $server, Site $site)
    {
        $deploymentService = 'php';

        return new $this->deploymentServices[$deploymentService]($this->remoteTaskService, $server, $site);
    }

    /**
     * @param Server     $server
     * @param SiteWorker $siteWorker
     *
     * @return array
     */
    public function installWorker(Server $server, SiteWorker $siteWorker)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->writeToFile('/etc/supervisor/conf.d/site-worker-'.$siteWorker->id.'.conf ', '
[program:site-worker-'.$siteWorker->id.']
process_name=%(program_name)s_%(process_num)02d
command='.$siteWorker->command.'
autostart='.$siteWorker->auto_start.'
autorestart='.$siteWorker->auto_restart.'
user='.$siteWorker->user.'
numprocs='.$siteWorker->number_of_workers.'
redirect_stderr=true
stdout_logfile=/home/codepier/workers/site-worker-'.$siteWorker->id.'.log
');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');
        $this->remoteTaskService->run('supervisorctl start site-worker-'.$siteWorker->id.':*');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server     $server
     * @param SiteWorker $siteWorker
     *
     * @return array|bool
     */
    public function removeWorker(Server $server, SiteWorker $siteWorker)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->removeFile("/etc/supervisor/conf.d/site-worker-$siteWorker->id.conf");

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');

        $errors = $this->remoteTaskService->getErrors();

        if (empty($errors)) {
            $siteWorker->delete();

            return true;
        }

        return $errors;
    }

    /**
     * @param Server $server
     * @param Site   $site
     *
     * @return array|bool
     */
    public function deleteSite(Server $server, Site $site)
    {
        // TODO this needs to be moved somewhere else
        dd('delete site');
//        foreach ($site->daemons as $daemon) {
//            $this->removeDaemon($server, $daemon);
//        }

        $errors = $this->remoteTaskService->getErrors();

        if (empty($errors)) {
            $errors = $this->remove($server, $site);
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
     * @param Server $server
     * @param Site   $site
     * @param $megabytes
     *
     * @return array
     */
    public function updateMaxUploadSize(Server $server, Site $site, $megabytes)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->writeToFile('/etc/nginx/conf.d/uploads.conf',
            'client_max_body_size '.$megabytes.'M;'
        );
        $this->remoteTaskService->removeLineByText('/etc/php/7.0/fpm/php.ini', 'upload_max_filesize = .*',
            'upload_max_filesize = '.$megabytes.'M');
        $this->remoteTaskService->removeLineByText('/etc/php/7.0/fpm/php.ini', 'post_max_size = .*',
            'post_max_size = '.$megabytes.'M');

        $this->remoteTaskService->run('service nginx restart');
        $this->remoteTaskService->run('service php7.0-fpm restart');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param Site   $site
     *
     * @return array
     */
    public function updateSiteNginxConfig(Server $server, Site $site)
    {
        $site->load('activeSSL');

        $this->remoteTaskService->ssh($server);

        if ($site->hasActiveSSL()) {
            $this->remoteTaskService->writeToFile('/etc/nginx/codepier-conf/'.$site->domain.'/server/listen', '
server_name '.($site->wildcard_domain ? '.' : '').$site->domain.';
listen 443 ssl http2 '.($site->domain == 'default' ? 'default_server' : null).';
listen [::]:443 ssl http2 '.($site->domain == 'default' ? 'default_server' : null).';

root /home/codepier/'.$site->domain.($site->zerotime_deployment ? '/current' : null).'/'.$site->web_directory.';


ssl_certificate_key /etc/nginx/ssl/'.$site->domain.'/'.$site->activeSSL->id.'/server.key;
ssl_certificate /etc/nginx/ssl/'.$site->domain.'/'.$site->activeSSL->id.'/server.crt;

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

            $this->remoteTaskService->writeToFile('/etc/nginx/codepier-conf/'.$site->domain.'/before/ssl_redirect.conf', '
server {
    listen 80 '.($site->domain == 'default' ? 'default_server' : null).';
    listen [::]:80 '.($site->domain == 'default' ? 'default_server' : null).';
    return 301 https://$host$request_uri;
}
');
        } else {
            $this->remoteTaskService->writeToFile('/etc/nginx/codepier-conf/'.$site->domain.'/server/listen', '
server_name '.($site->wildcard_domain ? '.' : '').$site->domain.';
listen 80 '.($site->domain == 'default' ? 'default_server' : null).';
listen [::]:80 '.($site->domain == 'default' ? 'default_server' : null).';

root /home/codepier/'.$site->domain.($site->zerotime_deployment ? '/current' : null).'/'.$site->web_directory.';
');
        }

        $this->remoteTaskService->run('service nginx restart');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param $domain
     *
     * @return bool
     */
    private function createNginxSite($domain)
    {
        return $this->remoteTaskService->writeToFile('/etc/nginx/sites-enabled/'.$domain, '
# codepier CONFIG (DO NOT REMOVE!)
include codepier-conf/'.$domain.'/before/*;

server {
    include codepier-conf/'.$domain.'/server/*;

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
include codepier-conf/'.$domain.'/after/*;
');
    }

    // TODO - after we fix ssls stuff

    public function checkSSL()
    {
        //        openssl x509 -in /etc/letsencrypt/live/codepier.io/cert.pem -noout -enddate
    }

    /**
     * @param Site $site
     */
    public function createDeployHook(Site $site)
    {
        $this->repositoryService->createDeployHook($site);
    }

    /**
     * @param Site $site
     */
    public function deleteDeployHook(Site $site)
    {
        $this->repositoryService->deleteDeployHook($site);
    }
}
