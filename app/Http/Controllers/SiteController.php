<?php

namespace App\Http\Controllers;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\Server\Site\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Jobs\CreateSite;
use App\Models\Server;
use App\Models\ServerDaemon;
use App\Models\Site;
use App\Models\SiteDaemon;

/**
 * Class SiteController
 * @package App\Http\Controllers
 */
class SiteController extends Controller
{
    protected $siteService;
    protected $serverService;
    protected $repositoryService;

    /**
     * SiteController constructor.
     * @param \App\Services\Server\Site\Repository\RepositoryService | RepositoryService $repositoryService
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\Server\Site\SiteService |SiteService $siteService
     */
    public function __construct(
        RepositoryService $repositoryService,
        ServerService $serverService,
        SiteService $siteService
    ) {
        $this->siteService = $siteService;
        $this->serverService = $serverService;
        $this->repositoryService = $repositoryService;
    }

    /**
     * Gets a site based on its id
     * @param $serverID
     * @param $siteID
     * @return mixed
     */
    public function getSite($serverID, $siteID)
    {
        $this->repositoryService->getUserRepositories(\Auth::user());

        return view('server.site.index', [
            'site' => Site::with('server')->findOrFail($siteID)
        ]);
    }

    /**
     * Creates a new site
     * @return mixed
     */
    public function postCreateSite($serverID)
    {
        $server = Server::findOrFail($serverID);

        $this->dispatch(new CreateSite($server, \Request::get('domain'), (int) \Request::get('wildcard_domain')));
//            ->onQueue('site_creations'));

        return back()->with('success', 'You have created a new server, we notify you when the provisioning is done');
    }

    /**
     * Installs a repository for a site
     * @param $serverID
     * @param $siteID
     * @return mixed
     */
    public function postInstallRepository($serverID, $siteID)
    {
        $repository = \Request::get('repository');

        $site = Site::with('server')->findOrFail($siteID);

        $sshKey = $this->serverService->getFile($site->server, '/home/codepier/.ssh/id_rsa.pub');

        if (empty($sshKey)) {
            return back()->withErrors('You seem to be missing a SSH key, please contact support.');
        }

        $this->repositoryService->importSshKey(\Auth::user()->userRepositoryProviders->first(), $repository, $sshKey);

        $site->repository = $repository;
        $site->branch = \Request::get('branch');

        $site->save();

        return back()->with('success', 'We have added the repository');
    }

    /**
     * Requests a SSL certificate on a server
     * @param $serverID
     * @param $siteID
     * @return $this
     */
    public function postRequestLetsEncryptSSLCert($serverID, $siteID)
    {
        $errors = $this->siteService->installSSL(Site::with('server')->findOrFail($siteID), \Request::get('domains'));

        if (is_array($errors)) {
            return back()->withErrors($errors);
        }

        return back()->with('success', 'You have succsefully instlled your ssl cert');
    }

    /**
     * Renames a sites domain
     * @param $serverID
     * @param $siteID
     * @return mixed
     */
    public function postRenameDomain($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        $this->siteService->renameDomain($site, \Request::get('domain'));

        return back()->with('success', 'Updated name');
    }

    /**
     * Updates a sites environment file
     * @param $serverID
     * @param $siteID
     * @return mixed
     */
    public function postEnv($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        $this->siteService->updateEnv($site, \Request::get('env'));

        return back()->with('success', 'Updated Env');
    }

    /**
     * Gets the env for a site
     * @param $serverID
     * @param $siteID
     * @return mixed
     */
    public function getEnv($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        return $this->serverService->getFile($site->server, $site->path . '/.env');
    }

    /**
     * Creates the deployment
     * @param $serverID
     * @param $siteID
     * @return mixed
     */
    public function getDeploy($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        $this->siteService->deploy($site->server, $site);

        return back()->with('success', 'we are currently deploying');
    }

    public function postInstallWorker($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        $this->siteService->installDaemon(
            $site,
            $site->path . ($site->zerotime_deployment ? '/current' : null) .'/artisan queue:work '.\Request::get('connection').' --timeout='.\Request::get('timeout').' --sleep='.\Request::get('sleep').' --tries='.\Request::get('tries').' '.(\Request::get('daemon') ? '--daemon' : null),
            true,
            true,
            'codepier',
            1
        );

        return back()->with('success', 'You have added a worker');
    }

    public function getRemoveWorker($serverID, $siteID, $workerID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        $this->siteService->removeDaemon($site->server, SiteDaemon::findOrFail($workerID));

        return back()->with('success', 'You have removed the worker');
    }

}
