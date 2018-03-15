<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Site\SiteSslCertificateCreated;

class SiteWildcardSslController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $siteId
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request, $siteId, $id)
    {
        $site = Site::with('sslCertificates')->findOrFail($siteId);

        $sslCertificate = $site->sslCertificates->keyBy('id')->get($id);

        $sslCertificate->update([
            'active' => true,
        ]);

        event(new SiteSslCertificateCreated($site, $sslCertificate));

        return response()->json($sslCertificate);
    }
}
