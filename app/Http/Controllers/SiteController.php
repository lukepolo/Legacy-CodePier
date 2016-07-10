<?php

namespace App\Http\Controllers;

use App\Contracts\Server\Site\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Http\Requests;
use App\Jobs\CreateSite;
use App\Models\Server;
use App\Models\Site;

/**
 * Class SiteController
 * @package App\Http\Controllers
 */
class SiteController extends Controller
{
    protected $siteService;
    protected $repositoryService;

    /**
     * SiteController constructor.
     *
     * @param \App\Services\Server\Site\Repository\RepositoryService | RepositoryService $repositoryService
     * @param \App\Services\Server\Site\SiteService |SiteService $siteService
     */
    public function __construct(RepositoryService $repositoryService, SiteService $siteService)
    {
        $this->repositoryService = $repositoryService;
        $this->siteService = $siteService;
    }

    /**
     * Gets a site based on its id
     *
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
     *
     * @return mixed
     */
    public function postCreateSite($serverID)
    {
        $server = Server::findOrFail($serverID);
        $domain = \Request::get('domain');

        Site::create([
            'domain' => $domain,
            'server_id' => $server->id,
            'wildcard_domain' => false,
            'zerotime_deployment' => false,
            'user_id' => $server->user_id,
            'path' => '/home/codepier/' . $domain
        ]);
        
        $this->dispatch((new CreateSite($server,$domain))->onQueue('site_creations'));

        return back()->with('success', 'You have created a new server, we notify you when the provisioning is done');
    }

    /**
     * Installs a repository for a site
     *
     * @param $serverID
     * @param $siteID
     * @return mixed
     */
    public function postInstallRepository($serverID, $siteID)
    {
        $repository = \Request::get('repository');

        $site = Site::with('server')->findOrFail($siteID);

        $sshKey = $this->siteService->getFile($site->server, '/home/codepier/.ssh/id_rsa.pub');

        if (empty($sshKey)) {
            return back()->withErrors('You seem to be missing a SSH key, please contact support.');
        }

        $this->repositoryService->importSshKey(\Auth::user()->userRepositoryProviders->first(), $repository, $sshKey);

        $site->repository = $repository;
        $site->branch = \Request::get('branch');

        $site->save();

        return back()->with('success', 'We have added the repository');
    }
    
    public function postRequestLetsEncryptSSLCert($serverID, $siteID)
    {
        $errors = $this->siteService->installSSL(Site::with('server')->findOrFail($siteID));

        if(is_array($errors)) {
            return back()->withErrors($errors);
        }

        return back()->with('success', 'You have succsefully instlled your ssl cert');
    }

    public function postRenameDomain($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        $this->siteService->renameDomain($site, \Request::get('domain'));

        return back()->with('success', 'Updated name');
    }
    
    public function postEnv($serverID, $siteID) 
    {
        $site = Site::with('server')->findOrFail($siteID);

        $this->siteService->updateEnv($site, \Request::get('env'));

        return back()->with('success', 'Updated Env');
    }

    /**
     * Gets the env for a site
     *
     * @param $serverID
     * @param $siteID
     * @return mixed
     */
    public function getEnv($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        return $this->siteService->getFile($site->server, $site->path.'/.env');
    }

    /**
     * Creates the deployment // TODO - ajax
     *
     * @param $serverID
     * @param $siteID
     *
     * @return mixed
     */
    public function getDeploy($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        $this->siteService->deploy($site->server, $site);

        return back()->with('success', 'we are currently deploying');
    }
}
