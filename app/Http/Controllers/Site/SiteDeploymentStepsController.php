<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Site\Deployment\DeploymentStep;
use App\Models\Site\Site;
use Illuminate\Http\Request;
use ReflectionClass;

class SiteDeploymentStepsController extends Controller
{
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
     * @param Request $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $site->deploymentSteps()->delete();

        $deploymentSteps = $this->buildDeploymentOptions($this->getDeploymentClasses($site))->keyBy('name');

        $order = 0;

        foreach ($request->get('deploymentSteps') as $deploymentStep) {
            $deploymentStep = $deploymentSteps->get($deploymentStep);

            DeploymentStep::create([
                'site_id' => $site->id,
                'order' => ++$order,
                'step' => $deploymentStep['name'],
                'internal_deployment_function' => $deploymentStep['task'],
            ]);
        }

        return response()->json('OK');
    }

    /**
     * @param $site
     * @return array
     */
    private function getDeploymentClasses($site)
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
     * @return array
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
}
