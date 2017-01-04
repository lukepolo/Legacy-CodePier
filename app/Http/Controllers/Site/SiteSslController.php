<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Site\Site;
use App\Models\SslCertificate;
use App\Services\Server\ServerService;
use App\Http\Requests\Site\SiteSslRequest;

class SiteSslController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Site::findOrFail($siteId)->sslCertificates
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SiteSslRequest $request
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(SiteSslRequest $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        switch ($type = $request->get('type')) {
            case ServerService::LETS_ENCRYPT:

                    $folder = explode(',', $request->get('domains'))[0];

                    $sslCertificate = SslCertificate::create([
                        'domains'   => $request->get('domains'),
                        'type'      => $request->get('type'),
                        'active'    => false,
                        'key_path'  => "/etc/letsencrypt/live/$folder/privkey.pem",
                        'cert_path' => "/etc/letsencrypt/live/$folder/fullchain.pem",
                    ]);

                break;
            case 'existing':

                $sslCertificate = SslCertificate::create([
                    'type'      => $request->get('type'),
                    'active'    => false,
                    'key' => $request->get('key'),
                    'cert' => $request->get('cert'),
                ]);
                break;
        }

        $site->sslCertificates()->save($sslCertificate);

        return response()->json($sslCertificate);
    }

    /**
     * @param SiteSslRequest $request
     * @param $siteId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SiteSslRequest $request, $siteId, $id)
    {
        $siteSslCertificate = Site::findOrFail($siteId)->get($id);

        return response()->json($siteSslCertificate->update([
            'active' => $request->get('active'),
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
            Site::findOrFail($siteId)->get($id)->get($id)
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
            Site::findOrFail($siteId)->get($id)->get($id)->delete()
        );
    }
}
