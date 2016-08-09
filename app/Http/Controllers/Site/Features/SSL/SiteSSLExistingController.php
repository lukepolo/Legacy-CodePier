<?php

namespace App\Http\Controllers;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Models\Site;
use Illuminate\Http\Request;

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
