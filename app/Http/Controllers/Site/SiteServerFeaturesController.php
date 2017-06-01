<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Services\Systems\SystemService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SiteServerFeaturesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Site::findOrFail($siteId)->server_features
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @param $siteId
     * @param $serverType
     * @return \Illuminate\Http\Response
     */
    public function show($siteId, $serverType)
    {
        return response()->json(
            collect(
                Site::findOrFail($siteId)->server_features
            )->only(
                SystemService::SERVER_TYPE_FEATURE_GROUPS[$serverType]
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $site->update([
            'server_features' => $request->get('services'),
        ]);

        return response()->json(
           $site->server_features
        );
    }
}
