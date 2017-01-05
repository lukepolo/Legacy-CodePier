<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Models\SslCertificate;
use App\Http\Requests\SslRequest;
use App\Http\Controllers\Controller;
use App\Services\Server\ServerService;
use App\Events\Site\SiteSslCertificateCreated;
use App\Events\Site\SiteSslCertificateDeleted;
use App\Events\Site\SiteSslCertificateUpdated;

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
     * @param SslRequest $request
     * @param $siteId
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(SslRequest $request, $siteId)
    {
        $site = Site::with('sslCertificates')->findOrFail($siteId);

        if(!$site->sslCertificates
            ->where('type', $this->sslCertificate->type)
            ->where('domains', $this->sslCertificate->domains)
            ->count()
        ) {
            switch ($type = $request->get('type')) {
                case ServerService::LETS_ENCRYPT:

                    $folder = explode(',', $request->get('domains'))[0];

                    $sslCertificate = SslCertificate::create([
                        'domains' => $request->get('domains'),
                        'type' => $request->get('type'),
                        'active' => false,
                        'key_path' => "/etc/letsencrypt/live/$folder/privkey.pem",
                        'cert_path' => "/etc/letsencrypt/live/$folder/fullchain.pem",
                    ]);

                    break;
                case 'existing':

                    $sslCertificate = SslCertificate::create([
                        'type' => $request->get('type'),
                        'active' => false,
                        'key' => $request->get('key'),
                        'cert' => $request->get('cert'),
                    ]);
                    break;
                default:
                    throw new \Exception('Invalid SSL Type');
                    break;
            }

            $site->sslCertificates()->save($sslCertificate);

            event(new SiteSslCertificateCreated($site, $sslCertificate));

            return response()->json($sslCertificate);
        }

        return response()->json('SSL Certificate for these domains Already Exists', 400);

    }

    /**
     * @param SslRequest $request
     * @param $siteId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SslRequest $request, $siteId, $id)
    {
        $site = Site::with('sslCertificates')->findOrFail($siteId);

        $sslCertificate = $site->sslCertificates->keyBy('id')->get($id);

        $sslCertificate->update([
            'active' => $request->get('active'),
        ]);

        event(new SiteSslCertificateUpdated($site, $sslCertificate));

        return response()->json($sslCertificate);
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
        $site = Site::with('sslCertificates')->findOrFail($siteId);

        event(new SiteSslCertificateDeleted($site, $site->sslCertificates->keyBy('id')->get($id)));

        return response()->json($site->sslCertificates()->detach($id));
    }
}
