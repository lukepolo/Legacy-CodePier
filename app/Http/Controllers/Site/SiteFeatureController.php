<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Contracts\Site\SiteFeatureServiceContract as SiteFeatureService;

class SiteFeatureController extends Controller
{
    private $siteFeatureService;

    /**
     * SiteFeatureController constructor.
     * @param \App\Services\Site\SiteFeatureService | SiteFeatureService $siteFeatureService
     */
    public function __construct(SiteFeatureService $siteFeatureService)
    {
        $this->siteFeatureService = $siteFeatureService;
    }

    /**
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEditableFrameworkFiles($siteId)
    {
        return response()->json($this->siteFeatureService->getEditableFrameworkFiles(Site::findOrFail($siteId)));
    }

    /**
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEditableFiles($siteId)
    {
        return response()->json($this->siteFeatureService->getEditableFiles(Site::findOrFail($siteId)));
    }
}
