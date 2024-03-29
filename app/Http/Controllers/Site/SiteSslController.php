<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Models\SslCertificate;
use App\Http\Requests\SslRequest;
use App\Http\Controllers\Controller;
use App\Services\Server\ServerService;
use App\Http\Requests\SslActivateRequest;
use App\Events\Site\SiteSslCertificateCreated;
use App\Events\Site\SiteSslCertificateDeleted;
use App\Events\Site\SiteSslCertificateUpdated;
use App\Services\Site\AcmeDnsService;

class SiteSslController extends Controller
{
    private $acmeDnsService;

    /**
     * SiteSslController constructor.
     * @param AcmeDnsService $acmeDnsService
     */
    public function __construct(AcmeDnsService $acmeDnsService)
    {
        $this->acmeDnsService = $acmeDnsService;
    }

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
        $domains = $request->domains;
        $domainString = implode(', ', $domains);

        $site = Site::with(['sslCertificates'])->findOrFail($siteId);

        $wildcard = false;
        $dontReturn = false;

        switch ($type = $request->get('type')) {
            case ServerService::LETS_ENCRYPT:

                $wildcard = $request->get('wildcard', false);
                $folder = $domains[0];
                $sslCertificate = $site->letsEncryptSslCertificates()
                    ->where('domains', $domainString)
                    ->where('wildcard', $wildcard)
                    ->first();

                if ($sslCertificate && $folder == explode(',', $sslCertificate->domains)[0]) {
                    $dontReturn = true;
                } else {
                    $sslCertificate = SslCertificate::create([
                        'domains' => $domainString,
                        'type' => $type,
                        'active' => true,
                        'wildcard' => $wildcard,
                    ]);

                    if ($wildcard) {
                        $registrationDetails = $this->acmeDnsService->register();
                        $sslCertificate->update([
                            'active' => 0,
                            'failed' => true,
                            'acme_username' => $registrationDetails->username,
                            'acme_password' => $registrationDetails->password,
                            'acme_subdomain' => $registrationDetails->subdomain,
                            'acme_fulldomain' => $registrationDetails->fulldomain,
                        ]);
                    }
                }

                break;
            case 'existing':
                $sslCertificate = SslCertificate::create([
                    'domains' => $domainString,
                    'type' => $request->get('type'),
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

        if ($wildcard === false) {
            event(new SiteSslCertificateCreated($site, $sslCertificate));
        }

        if (! $dontReturn) {
            return response()->json($sslCertificate);
        }

        return response()->json('OK');
    }

    /**
     * @param SslActivateRequest $request
     * @param $siteId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SslActivateRequest $request, $siteId, $id)
    {
        $site = Site::with('sslCertificates')->findOrFail($siteId);

        $sslCertificate = $request->user()->availableSslCertificates()->get($id);

        if (empty($sslCertificate)) {
            $sslCertificate = $site->sslCertificates->where('id', $id)->first();
        } elseif (! $site->sslCertificates->where('id', $sslCertificate->id)->count()) {
            $site->sslCertificates()->attach($sslCertificate);
        }

        $sslCertificate->update([
            'failed' => false,
            'active' => $request->get('active'),
        ]);

        broadcast(new SiteSslCertificateUpdated($site, $sslCertificate));

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
