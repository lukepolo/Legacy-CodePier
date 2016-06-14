<?php

namespace App\Http\Controllers;

use App\Contracts\Server\Site\Repository\RepositoryServiceContract as RepositoryService;
use App\Http\Requests;
use App\Jobs\CreateSite;
use App\Models\Site;

/**
 * Class SiteController
 * @package App\Http\Controllers
 */
class SiteController extends Controller
{
    protected $repositoryService;

    /**
     * SiteController constructor.
     * @param \App\Services\Server\Site\Repository\RepositoryService | RepositoryService $repositoryService
     */
    public function __construct(RepositoryService $repositoryService)
    {
        $this->repositoryService = $repositoryService;
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
}
