<?php

namespace App\Http\Controllers\Site\Repository;

use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Exceptions\DeployHookFailed;
use App\Http\Controllers\Controller;
use App\Models\Site\Site;

class RepositoryHookController extends Controller
{
    private $siteService;

    /**
     * RepositoryHookController constructor.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $siteId
     *
     * @throws \App\Exceptions\SiteUserProviderNotConnected
     *
     * @return \Illuminate\Http\Response
     */
    public function store($siteId)
    {
        $site = Site::findOrFail($siteId);

        if (! $site->isValidRepository()) {
            return response()->json('Invalid', 400);
        }

        try {
            return response()->json(
                $this->siteService->createDeployHook($site)
            );
        } catch (DeployHookFailed $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId)
    {
        $site = Site::findOrFail($siteId);
        try {
            return response()->json(
                $this->siteService->deleteDeployHook($site)
            );
        } catch (\Exception $e) {
            $site->update([
                'automatic_deployment_id' => null,
            ]);

            return response()->json('We were unable to delete the webhook, you should remove this manually', 500);
        }
    }
}
