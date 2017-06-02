<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Systems\SystemService;

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
        $languages = collect();
        $serverFeatures = collect(Site::findOrFail($siteId)->server_features);
        $serverTypeFeatureGroups = collect(SystemService::SERVER_TYPE_FEATURE_GROUPS[$serverType]);

        if($serverTypeFeatureGroups->contains(SystemService::LANGUAGES_GROUP)) {
            $languages = $serverFeatures->filter(function($features, $featureIndex) {
                return starts_with($featureIndex, 'Languages');
            })->keys();
        }

        return response()->json(
            $serverFeatures->only(
                $languages->merge($serverTypeFeatureGroups)->toArray()
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
