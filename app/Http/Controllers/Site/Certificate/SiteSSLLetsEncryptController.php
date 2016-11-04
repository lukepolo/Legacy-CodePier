<?php

namespace App\Http\Controllers\Site\Certificate;

use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Http\Controllers\Controller;
use App\Models\Site\SiteSslCertificate;
use Illuminate\Http\Request;

/**
 * Class SiteSSLLetsEncryptController
 * @package App\Http\Controllers\Site\Certificate
 */
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
        $folder = explode(',', $request->get('domains'))[0];

        return response()->json(
            SiteSslCertificate::create([
                'site_id'   => $siteId,
                'domains'   => $request->get('domains'),
                'type'      => \App\Services\Systems\WebServers\NginxWebServerService::LETS_ENCRYPT,
                'active'    => false,
                'key_path'  => "/etc/letsencrypt/live/$folder/privkey.pem",
                'cert_path' => "/etc/letsencrypt/live/$folder/fullchain.pem",
            ])
        );
    }
}
