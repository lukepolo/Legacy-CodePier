<?php

namespace App\Http\Controllers\Site\Features\SSL;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteSslCertificate;
use Illuminate\Http\Request;

class SiteSSLLetsEncryptController extends Controller
{
    private $siteService;

    /**
     * SiteSSLController constructor.
     *
     * @param \App\Services\Server\Site\SiteService | SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $site = Site::findOrfail($request->get('site_id'));

        $folder = explode(',', $request->get('domains'))[0];
        $siteSSLCertificate = SiteSslCertificate::create([
            'site_id'   => $request->get('site_id'),
            'domains'   => $request->get('domains'),
            'type'      => \App\Services\Server\Site\SiteService::LETS_ENCRYPT,
            'active'    => false,
            'key_path'  => "/etc/letsencrypt/live/$folder/privkey.pem",
            'cert_path' => "/etc/letsencrypt/live/$folder/fullchain.pem",
        ]);

        foreach ($site->servers as $server) {
            $this->siteService->installSSL($server, $siteSSLCertificate);
        }

        return response()->json();
    }
}
