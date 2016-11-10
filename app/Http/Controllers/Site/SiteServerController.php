<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteServerRequest;
use App\Models\Site\Site;

class SiteServerController extends Controller
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
            Site::where('id', $siteId)->firstorFail()->servers
        );
    }

    /**
     * Stores a site.
     *
     * @param SiteServerRequest $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SiteServerRequest $request, $siteId)
    {
        $site = Site::where('id', $siteId)->firstorFail();

        $site->servers()->sync($request->get('connected_servers', []));

        $site->fire('saved');

        return response()->json($site);
    }
}
