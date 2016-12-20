<?php

namespace App\Http\Controllers\Site;

use ReflectionClass;
use App\Models\Site\Site;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Models\Site\Deployment\DeploymentStep;
use App\Http\Requests\Site\SiteDeploymentStepsRequest;

class SiteDeploymentStepsController extends Controller
{
    private $deploymentSteps = [];

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

        return response()->json($this->buildDeploymentOptions($this->getDeploymentClasses($site))->values()->all());
    }

    /**
     * @param SiteDeploymentStepsRequest $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SiteDeploymentStepsRequest $request, $siteId)
    {
        $site = Site::with('deploymentSteps')->findOrFail($siteId);

        $this->deploymentSteps = $this->buildDeploymentOptions($this->getDeploymentClasses($site))->keyBy('internal_deployment_function');

        $newDeploymentSteps = collect($request->get('deployment_steps'));

        // We must delete old deployment steps
        $site->deploymentSteps->each(function ($siteDeploymentStep) use ($newDeploymentSteps) {
            if (! $newDeploymentSteps->first(function ($deploymentStep) use ($siteDeploymentStep) {
                if (! empty($deploymentStep['script'])) {
                    return $siteDeploymentStep->script == $deploymentStep['script'];
                }

                $deploymentStep = $this->getDeploymentStep($deploymentStep);

                if ($deploymentStep) {
                    return $siteDeploymentStep->internal_deployment_function == $deploymentStep['internal_deployment_function'];
                }

                return false;
            })) {
                $siteDeploymentStep->delete();
            }
        });

        $order = 0;

        // We then attach the deployment steps with the new order they give
        foreach ($newDeploymentSteps as $deploymentStep) {
            $internalStep = $this->getDeploymentStep($deploymentStep);

            $deploymentStep = DeploymentStep::firstOrnew([
                'site_id' => $site->id,
                'step' => ! empty($internalStep) ? $internalStep['step'] : $deploymentStep['step'],
                'script' => empty($internalStep) ? $deploymentStep['script'] : null,
                'internal_deployment_function' => ! empty($internalStep) ? $internalStep['internal_deployment_function'] : null,
            ]);

            $deploymentStep->order = ++$order;
            $deploymentStep->save();
        }

        return response()->json('OK');
    }

    /**
     * @param $site
     * @return array
     */
    private function getDeploymentClasses(Site $site)
    {
        $classes = [$this->getSiteClass($site)];

        if (! empty($site->getFrameworkClass())) {
            $classes[] = $this->getFrameworkClass($site);
        }

        return $classes;
    }

    /**
     * @param Site $site
     * @return string
     */
    private function getSiteClass(Site $site)
    {
        return 'App\Services\DeploymentServices\\'.$site->getSiteLanguage();
    }

    /**
     * @param Site $site
     * @return string
     */
    private function getFrameworkClass(Site $site)
    {
        return 'App\Services\DeploymentServices\\'.$site->getFrameworkClass();
    }

    /**
     * @param array $classes
     * @return Collection
     */
    private function buildDeploymentOptions(array $classes)
    {
        $deploymentSteps = [];

        foreach ($classes as $class) {
            $reflection = new ReflectionClass($class);

            $traitMethods = [];

            foreach ($reflection->getTraits() as $reflectionTraitClass) {
                foreach ($reflectionTraitClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                    $traitMethods[] = $method->name;
                }
            }

            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->name != '__construct' && ! in_array($method->name, $traitMethods)) {
                    preg_match('/\@order\s(.*)/', $method->getDocComment(), $matches);

                    if (isset($matches[1])) {
                        $order = $matches[1];

                        preg_match('/\@description\s(.*)/', $method->getDocComment(), $matches);

                        $description = $matches[1];

                        $deploymentSteps[] = [
                            'order' => (int) $order,
                            'description' => $description,
                            'internal_deployment_function' => $method->name,
                            'step' => ucwords(str_replace('_', ' ', snake_case($method->name))),
                        ];
                    }
                }
            }
        }

        return collect($deploymentSteps)->sortBy('order');
    }

    /**
     * @param $deploymentStep
     * @return null
     */
    private function getDeploymentStep($deploymentStep)
    {
        return ! empty($deploymentStep['internal_deployment_function']) ? $this->deploymentSteps->get($deploymentStep['internal_deployment_function']) : null;
    }
}
