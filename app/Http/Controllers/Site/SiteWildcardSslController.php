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

        $baseDnsLookup = new \Spatie\Dns\Dns($sslCertificate->domains);
        $baseDnsLookup->useNameserver('8.8.8.8');

        $soaRecord = preg_replace('/\s+/', ' ', $baseDnsLookup->getRecords(['SOA']));
        preg_match('/SOA (.*?)\.\s/', $soaRecord, $SOA);

        $acmeDnsLookup = new \Spatie\Dns\Dns('_acme-challenge.'.$sslCertificate->domains);

        if (isset($SOA[1])) {
            $baseDnsLookup->useNameserver($SOA[1]);
        }

        $cnameRecords = preg_replace('/\s+/', ' ', $acmeDnsLookup->getRecords(['CNAME']));
        preg_match('/CNAME (.*?)\.\s/', $cnameRecords, $CNAME);

        if (isset($CNAME[1])) {
            if ($CNAME[1] === $sslCertificate->acme_fulldomain) {
                $sslCertificate->update([
                    'active' => true,
                ]);

                event(new SiteSslCertificateCreated($site, $sslCertificate));

                return response()->json($sslCertificate);
            }
        }

        return response()->json('You have not setup your CNAME host _acme-challenge.'.$sslCertificate->domains, 400);
    }
}
