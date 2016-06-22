<?php

namespace App\Http\Controllers;

use App\Contracts\Server\Site\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Http\Requests;
use App\Jobs\CreateSite;
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
    public function postCreateSite()
    {
        $this->dispatch((new CreateSite(\Request::get('domain')))->onQueue('site_creations'));

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

        $this->repositoryService->importSshKey('github', \Auth::user(), $repository, $sshKey);

        $site->repository = $repository;
        $site->branch = \Request::get('branch');

        $site->save();

        return back()->with('success', 'We have added the repository');
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

        return $this->siteService->getFile($site->server, '/home/codepier/default/.env');
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
