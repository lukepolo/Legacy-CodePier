<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Models\Site\SiteDeployment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteDeploymentRequest;

class SiteDeploymentsController extends Controller
{
    /**
     * @param $siteId
     *
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
     *
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

    /**
     * @param SiteDeploymentRequest $request
     * @param $siteId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SiteDeploymentRequest $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $site->update([
            'keep_releases'               => $request->get('keep_releases', 10),
            'zero_downtime_deployment'         => $request->get('zero_downtime_deployment', 0),
        ]);

        return response()->json($site);
    }
}
