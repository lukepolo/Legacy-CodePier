<?php

namespace App\Http\Controllers;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Jobs\DeploySite;
use App\Models\Site;
use App\Models\SiteSslCertificate;
use Illuminate\Http\Request;

/**
 * Class SiteController
 * @package App\Http\Controllers
 */
class SiteController extends Controller
{
    private $siteService;

    /**
     * SiteController constructor.
     * @param \App\Services\Server\Site\SiteService | SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * Activates a ssl certificate
     *
     * @param Request $request
     * @return $this
     */
    public function activateSSL(Request $request)
    {
        $errors = $this->siteService->activateSSL(SiteSslCertificate::findOrFail($request->get('site_ssl_certificate_id')));

        if (is_array($errors)) {
            return back()->withErrors($errors);
        }
    }

    /**
     * Deactivates a ssl certificate
     *
     * @param Request $request
     */
    public function deactivate(Request $request)
    {
        $this->siteService->deactivateSSL(Site::findOrFail($request->get('site_id')));
    }

    /**
     * Deploys a site
     *
     * @param Request $request
     */
    public function deploy(Request $request)
    {
        $site = Site::with('server')->findOrFail($request->get('site_id'));

        $this->dispatch(new DeploySite($site));
    }
}
