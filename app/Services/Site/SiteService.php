<?php

namespace App\Services\Site;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\Site\SiteServiceContract;
use App\Contracts\WebServers\NginxWebServerServiceContract;
use App\Events\Site\DeploymentStepCompleted;
use App\Events\Site\DeploymentStepFailed;
use App\Events\Site\DeploymentStepStarted;
use App\Exceptions\DeploymentFailed;
use App\Exceptions\FailedCommand;
use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Models\Site\SiteCronJob;
use App\Models\Site\SiteDeployment;
use App\Models\Site\SiteSslCertificate;
use App\Models\Site\SiteWorker;

/**
 * Class SiteService.
 */
class SiteService implements SiteServiceContract
{
    protected $serverService;
    protected $remoteTaskService;
    protected $repositoryService;

    const SSL_FILES = '/etc/opt/ssl';
    const LETS_ENCRYPT = 'Let\'s Encrypt';

    public $deploymentServices = [
        'php' => DeploymentLanguages\PHP::class,
    ];

    /**
     * SiteService constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     * @param \App\Services\Repository\RepositoryService | RepositoryService $repositoryService
     */
    public function __construct(ServerService $serverService, RemoteTaskService $remoteTaskService, RepositoryService $repositoryService)
    {
        $this->serverService = $serverService;
        $this->remoteTaskService = $remoteTaskService;
        $this->repositoryService = $repositoryService;
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site   $site
     *
     * @return bool
     */
    public function create(Server $server, Site $site)
    {
        $this->getWebServerService()->create($server, $site);

        $this->remoteTaskService->ssh($server, 'codepier');

        $this->remoteTaskService->makeDirectory('/home/codepier/'.$site->domain);

        $this->serverService->restartWebServices($server);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site   $site
     * @param $domain
     *
     * @return array
     */
    public function renameDomain(Server $server, Site $site, $domain)
    {
        // TODO
        dd('Needs to be tested');

        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('mv /home/codepier/'.$site->domain.' /home/codepier/'.$domain);

        $this->remove($server, $site);

        $this->create($server, $site);

        $site->domain = $domain;

        $site->save();

        $this->serverService->restartWebServices($server);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site   $site
     *
     * @return array
     */
    public function remove(Server $server, Site $site)
    {
        $this->getWebServerService()->remove($server, $site);

        return $this->remoteTaskService->getErrors();
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

    /**
     * @param \App\Models\Server\Server         $server
     * @param Site           $site
     * @param \App\Models\Site\SiteDeployment $siteDeployment
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
        $this->serverService->restartWebServices($server);

        // TODO - should be a notification
//        event(new DeploymentCompleted($site, $siteDeployment, 'Commit #####', $this->remoteTaskService->getOutput()));
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param \App\Models\Site\Site   $site
     *
     * @return mixed
     */
    private function getDeploymentService(Server $server, Site $site)
    {
        $deploymentService = 'php';

        return new $this->deploymentServices[$deploymentService]($this->remoteTaskService, $server, $site);
    }

    /**
     * @param \App\Models\Server\Server     $server
     * @param \App\Models\Site\SiteWorker $siteWorker
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
     * @param \App\Models\Server\Server     $server
     * @param \App\Models\Site\SiteWorker $siteWorker
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
     * @param SiteCronJob $siteCronJob
     */
    public function installCronJob(Server $server, SiteCronJob $siteCronJob)
    {

    }

    /**
     * @param Server $server
     * @param SiteCronJob $siteCronJob
     */
    public function removeCronJob(Server $server, SiteCronJob $siteCronJob)
    {

    }

    /**
     * @param \App\Models\Server\Server $server
     * @param \App\Models\Site\Site   $site
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
     * @param \App\Models\Server\Server $server
     * @param \App\Models\Site\Site   $site
     *
     * @return array
     */
    public function updateWebServerConfig(Server $server, Site $site)
    {
        $this->getWebServerService()->updateWebServerConfig($server, $site);

        $this->serverService->restartWebServices($server);

        return $this->remoteTaskService->getErrors();
    }

    // TODO - after we fix ssls stuff

    /**
     *
     */
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

    /**
     * Gets the web server based on their server sent in.
     *
     * @return mixed
     */
    private function getWebServerService()
    {
        // TODO - currently we only do NGINX
        return app()->make(NginxWebServerServiceContract::class);
    }
}
