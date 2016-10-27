<?php

namespace App\Http\Controllers\Site\Certificate;

use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Http\Controllers\Controller;
use App\Models\Site\Site;
use Illuminate\Http\Request;

/**
 * Class SiteSSLExistingController.
 */
class SiteSSLExistingController extends Controller
{
    private $siteService;

    /**
     * SiteSSLController constructor.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $siteId)
    {
        $this->siteService->installExistingSSL(
            Site::with('server')->findOrFail($siteId),
            $request->get('key'),
            $request->get('cert')
        );
    }
}
