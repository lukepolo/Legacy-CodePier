<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\SiteDeployment;
use App\Http\Controllers\Controller;

class SiteDeploymentsController extends Controller
{
    /**
     * @param $siteId
     * @return array
     */
    public function index($siteId)
    {
        return response()->json(
            SiteDeployment::where('site_id', $siteId)
                ->latest()
                ->take(10)
                ->get()
        );
    }

    /**
     * @param $siteId
     * @param $siteDeploymentId
     * @return array
     */
    public function show($siteId, $siteDeploymentId)
    {
        return response()->json(
            SiteDeployment::with([
                    'serverDeployments.events.step' => function ($query) {
                        $query->withTrashed();
                    }, ]
                )
                ->where('site_id', $siteId)
                ->findOrFail($siteDeploymentId)
        );
    }
}
