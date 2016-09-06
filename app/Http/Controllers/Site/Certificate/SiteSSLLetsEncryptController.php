<?php

namespace App\Http\Controllers\Site\Certificate;

use App\Contracts\Site\SiteServiceContract as SiteService;
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
        $site = Site::findOrfail($siteId);

        $folder = explode(',', $request->get('domains'))[0];
        $siteSSLCertificate = SiteSslCertificate::create([
            'site_id'   => $siteId,
            'domains'   => $request->get('domains'),
            'type'      => \App\Services\Site\SiteService::LETS_ENCRYPT,
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
