<?php

namespace App\Http\Controllers\Site\Features\SSL;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;

/**
 * Class SiteSSLExistingController
 * @package App\Http\Controllers\Site\Features\SSL
 */
class SiteSSLExistingController extends Controller

{
    private $siteService;

    /**
     * SiteSSLController constructor.
     * @param \App\Services\Server\Site\SiteService | SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->siteService->installExistingSSL(
            Site::with('server')->findOrFail($request->get('site_id')),
            $request->get('key'),
            $request->get('cert')
        );
    }
}
