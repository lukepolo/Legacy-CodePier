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
        $site = Site::findOrFail($siteId);

        return $this->buildDeploymentOptions('App\Services\DeploymentServices\\'.$site->getSiteLanguage(), 'App\Services\DeploymentServices\\'.$site->getFrameworkClass());
    }

    /**
     * @param Request $request
     * @param $siteId
     */
    public function store(Request $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $site->deploymentSteps()->delete();

        $order = 0;

        foreach ($request->get('deploymentSteps') as $deploymentStep) {
            $site->deploymentSteps()->save(DeploymentStep::create([
                'order' => ++$order,
                'step' => $deploymentStep['step'],
                'internal_deployment_function' => $deploymentStep['task']
            ]));
        }
    }

    /**
     * @param array ...$classes
     * @return array
     */
    private function buildDeploymentOptions(...$classes)
    {
        $deploymentSteps = [];

        foreach ($classes as $class) {
            $reflection = new ReflectionClass($class);

            // TODO - get description or something from comment
            foreach ($reflection->getMethods() as $method) {
                if ($method->name != '__construct') {
                    preg_match('/\@order\s(.*)/', $method->getDocComment(), $matches);
                    $order = $matches[1];

                    preg_match('/\@description\s(.*)/', $method->getDocComment(), $matches);
                    $description = $matches[1];

                    $deploymentSteps[] = [
                        'name' => ucwords(str_replace('_', ' ',snake_case($method->name))),
                        'order' => $order,
                        'task' => $method->name,
                        'description' => $description,
                    ];
                }
            }
        }

        return $deploymentSteps;
    }
}
