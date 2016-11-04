<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Site\SiteSslCertificate;
use Illuminate\Http\Request;

/**
 * Class SiteSSLController.
 */
class SiteSSLController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $siteId)
    {
        return response()->json(
            SiteSslCertificate::where('site_id', $siteId)->get()
        );
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
        $siteSslCertificate = false;

        switch($type = $request->get('type')) {
            case \App\Services\Systems\WebServers\NginxWebServerService::LETS_ENCRYPT :

                    $folder = explode(',', $request->get('domains'))[0];

                    $siteSslCertificate = SiteSslCertificate::create([
                        'site_id'   => $siteId,
                        'domains'   => $request->get('domains'),
                        'type'      => $request->get('type'),
                        'active'    => false,
                        'key_path'  => "/etc/letsencrypt/live/$folder/privkey.pem",
                        'cert_path' => "/etc/letsencrypt/live/$folder/fullchain.pem",
                    ]);

                break;
            case 'existing' :

                $siteSslCertificate = SiteSslCertificate::create([
                    'site_id'   => $siteId,
                    'type'      => $request->get('type'),
                    'active'    => false,
                    'key' => $request->get('key'),
                    'cert' => $request->get('cert')
                ]);
                break;
        }

        return response()->json($siteSslCertificate);
    }

    /**
     * @param Request $request
     * @param $siteId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $siteId, $id)
    {
        $siteSslCertificate = SiteSslCertificate::where('site_id', $siteId)->findOrFail($id);

        return response()->json($siteSslCertificate->update([
            'active' => $request->get('active')
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($siteId, $id)
    {
        return response()->json(
            SiteSslCertificate::where('site_id', $siteId)->findOrFail($id)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId, $id)
    {
        return response()->json(
            SiteSslCertificate::where('site_id', $siteId)->findOrFail($id)->delete()
        );
    }
}
