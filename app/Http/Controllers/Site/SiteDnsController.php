<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class SiteDnsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        if (is_domain($site->domain)) {
            $key = $siteId.'_dns';

            if ($request->has('refresh')) {
                Cache::forget($key);
            }

            return response()->json(
                Cache::remember($key, 60 * 24, function () use ($site) {
                    return collect(dns_get_record($site->domain, DNS_A))->first(function ($record) use ($site) {
                        return $record['host'] == $site->domain;
                    });
                })
            );
        }

        return response()->json([]);
    }
}
