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

        $cnames = dns_get_record('_acme-challenge.'.$sslCertificate->domains, DNS_CNAME);

        $valid = false;

        if (! empty($cnames)) {
            foreach ($cnames as $cname) {
                if ($cname['target'] === $sslCertificate->acme_fulldomain) {
                    $valid = true;
                }
            }
        }

        if (! $valid) {
            return response()->json('You have not setup your CNAME host _acme-challenge'.$sslCertificate->domains, 400);
        }

        $sslCertificate->update([
            'active' => true,
        ]);

        event(new SiteSslCertificateCreated($site, $sslCertificate));

        return response()->json($sslCertificate);
    }
}
