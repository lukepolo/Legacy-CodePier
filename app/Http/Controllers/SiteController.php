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
     * @param \App\Services\Server\Site\Repository\RepositoryService | RepositoryService $repositoryService
     * @param \App\Services\Server\Site\SiteService |SiteService $siteService
     */
    public function __construct(RepositoryService $repositoryService, SiteService $siteService)
    {
        $this->repositoryService = $repositoryService;
        $this->siteService = $siteService;
    }

    public function getSite($serverID, $siteID)
    {
        $this->repositoryService->getRepositories('github', \Auth::user());

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

    public function getEnv($serverID, $siteID)
    {
        $site = Site::with('server')->findOrFail($siteID);

        return $this->siteService->getFile($site->server, '/home/codepier/default/.env');
    }
}
