<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
    public function store(Request $request, $siteId)
    {

//        curl -s -X POST https://auth.acme-dns.io/register
//        {
//            "allowfrom": [],
//    "fulldomain": "c91f16ff-a31e-4795-a36f-33714ce661c3.auth.acme-dns.io",
//    "password": "46bYuPj0tqIi6bOpZFCZxPABhShQJTLDfrIFQlga",
//    "subdomain": "c91f16ff-a31e-4795-a36f-33714ce661c3",
//    "username": "e324dfae-48f4-425e-9795-f48380ca9674"
//}

        // Step 2 - create CNAME using full domain


        // Step 3 - get token from CertBot-Auto

        // Step 4 - Update txt record with credentials

        // Step 5 - Get / Update  Certificate
    }
}
