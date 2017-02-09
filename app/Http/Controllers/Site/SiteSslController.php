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
        $domains = $request->get('domains');
        $site = Site::with(['sslCertificates'])->findOrFail($siteId);

        $dontReturn = false;

        switch ($type = $request->get('type')) {
            case ServerService::LETS_ENCRYPT:

                $folder = explode(',', $request->get('domains'))[0];
                $sslCertificate = $site->letsEncryptSslCertificates()->where('domains', $domains)->first();

                if ($sslCertificate && $folder == explode(',', $sslCertificate->domains)[0]) {
                    $dontReturn = true;
                } else {
                    $sslCertificate = SslCertificate::create([
                        'domains' => $domains,
                        'type' => $type,
                        'active' => false,
                        'key_path' => "/etc/letsencrypt/live/$folder/privkey.pem",
                        'cert_path' => "/etc/letsencrypt/live/$folder/fullchain.pem",
                    ]);
                }

                break;
            case 'existing':
                $sslCertificate = SslCertificate::create([
                    'domains' => $domains,
                    'type' => $request->get('type'),
                    'active' => false,
                    'key' => $request->get('private_key'),
                    'cert' => $request->get('certificate'),
                ]);
                break;
            default:
                throw new \Exception('Invalid SSL Type');
                break;
        }

        if (! $site->sslCertificates->where('id', $sslCertificate->id)->count()) {
            $site->sslCertificates()->attach($sslCertificate);
        }

        event(new SiteSslCertificateCreated($site, $sslCertificate));

        if (! $dontReturn) {
            return response()->json($sslCertificate);
        }

        return response()->json('OK');
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

        event(new SiteSslCertificateUpdated($site, $sslCertificate, $request->get('active')));

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
