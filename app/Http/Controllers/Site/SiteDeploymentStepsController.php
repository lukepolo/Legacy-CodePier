<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteDeploymentStepsRequest;
use App\Contracts\Site\SiteDeploymentStepsServiceContract as SiteDeploymentStepsService;

class SiteDeploymentStepsController extends Controller
{
    private $siteDeploymentStepsService;

    /**
     * SiteDeploymentStepsController constructor.
     * @param \App\Services\Site\SiteDeploymentStepsService |SiteDeploymentStepsService $siteDeploymentStepsService
     */
    public function __construct(SiteDeploymentStepsService $siteDeploymentStepsService)
    {
        $this->siteDeploymentStepsService = $siteDeploymentStepsService;
    }

    /**
     * @param $siteId
     * @return array
     * @internal param Request $request
     */
    public function index($siteId)
    {
        return response()->json(Site::findOrFail($siteId)->deploymentSteps);
    }

    /**
     * @param $siteId
     * @return array
     */
    public function getDeploymentSteps($siteId)
    {
        $site = Site::findOrFail($siteId);

        return response()->json($this->siteDeploymentStepsService->buildDeploymentOptions($this->siteDeploymentStepsService->getDeploymentClass($site))->values()->all());
    }

    /**
     * @param SiteDeploymentStepsRequest $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SiteDeploymentStepsRequest $request, $siteId)
    {
        $site = Site::with('deploymentSteps')->findOrFail($siteId);

        $this->deploymentSteps = $this->siteDeploymentStepsService->buildDeploymentOptions($this->siteDeploymentStepsService->getDeploymentClass($site))->keyBy('internal_deployment_function');

        $newDeploymentSteps = collect($request->get('deployment_steps'));

        // We must delete old deployment steps
        $site->deploymentSteps->each(function ($siteDeploymentStep) use ($site, $newDeploymentSteps) {
            if (! $newDeploymentSteps->first(function ($deploymentStep) use ($site, $siteDeploymentStep) {
                if (! empty($deploymentStep['script'])) {
                    return $siteDeploymentStep->script == $deploymentStep['script'];
                }

                $deploymentStep = $this->siteDeploymentStepsService->getDeploymentStep($site, $deploymentStep);

                if ($deploymentStep) {
                    return $siteDeploymentStep->internal_deployment_function == $deploymentStep['internal_deployment_function'];
                }

                return false;
            })) {
                $siteDeploymentStep->delete();
            }
        });

        $this->siteDeploymentStepsService->saveNewSteps($site, $newDeploymentSteps);

        return response()->json('OK');
    }
}
