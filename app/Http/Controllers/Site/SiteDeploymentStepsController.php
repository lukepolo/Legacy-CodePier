<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteDeploymentStepsRequest;
use App\Models\Site\Deployment\DeploymentStep;
use App\Models\Site\Site;
use Illuminate\Support\Collection;
use ReflectionClass;

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

        $this->deploymentSteps = $this->buildDeploymentOptions($this->getDeploymentClasses($site))->keyBy('name');

        $newDeploymentSteps = collect($request->get('deploymentSteps'));

        $site->deploymentSteps->each(function ($siteDeploymentStep) use ($newDeploymentSteps) {
            if (! $newDeploymentSteps->first(function ($deploymentStep) use ($siteDeploymentStep) {
                if (! empty($siteDeploymentStep->script)) {
                    return $siteDeploymentStep->script == $this->getScript($deploymentStep);
                }

                return $siteDeploymentStep->internal_deployment_function == $this->getInternalDeploymentFunction($deploymentStep);
            })) {
                $siteDeploymentStep->delete();
            }
        });

        $order = 0;

        foreach ($newDeploymentSteps as $deploymentStep) {
            $deploymentStep = DeploymentStep::firstOrnew([
                'site_id' => $site->id,
                'step' => $this->getStep($deploymentStep),
                'script' => $this->getScript($deploymentStep),
                'internal_deployment_function' => $this->getInternalDeploymentFunction($deploymentStep),
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
        $classes = [
            'App\Services\DeploymentServices\\'.$site->getSiteLanguage(),
        ];

        if (! empty($site->getFrameworkClass())) {
            $classes[] = 'App\Services\DeploymentServices\\'.$site->getFrameworkClass();
        }

        return $classes;
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
                    $order = $matches[1];

                    preg_match('/\@description\s(.*)/', $method->getDocComment(), $matches);
                    $description = $matches[1];

                    $deploymentSteps[] = [
                        'name' => ucwords(str_replace('_', ' ', snake_case($method->name))),
                        'order' => (int) $order,
                        'task' => $method->name,
                        'description' => $description,
                    ];
                }
            }
        }

        return collect($deploymentSteps)->sortBy('order');
    }

    /**
     * @param $deploymentStep
     * @return null
     */
    private function getScript($deploymentStep)
    {
        return ! $this->deploymentSteps->has($deploymentStep) ? $deploymentStep : null;
    }

    /**
     * @param $deploymentStep
     * @return null
     */
    private function getInternalDeploymentFunction($deploymentStep)
    {
        return $this->deploymentSteps->has($deploymentStep) ? $this->deploymentSteps->get($deploymentStep)['task'] : null;
    }

    /**
     * @param $deploymentStep
     * @return string
     */
    private function getStep($deploymentStep)
    {
        return $this->deploymentSteps->has($deploymentStep) ? $this->deploymentSteps->get($deploymentStep)['name'] : 'Custom Step';
    }
}
