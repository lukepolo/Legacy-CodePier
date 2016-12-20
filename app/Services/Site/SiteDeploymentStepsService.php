<?php

namespace App\Services\Site;

use App\Contracts\Site\SiteDeploymentStepsServiceContract;
use App\Models\Site\Deployment\DeploymentStep;
use App\Traits\SystemFiles;
use ReflectionClass;
use App\Models\Site\Site;
use Illuminate\Support\Collection;

class SiteDeploymentStepsService implements SiteDeploymentStepsServiceContract
{
    use SystemFiles;

    private $deploymentSteps = [];

    /**
     * Gets all deployment classes for their site
     * @param $site
     * @return array
     */
    public function getDeploymentClasses(Site $site)
    {
        $classes = [$this->getSiteClass($site)];

        if (! empty($site->getFrameworkClass())) {
            $classes[] = $this->getFrameworkClass($site);
        }

        return $classes;
    }

    /**
     * Gest the sites class
     * @param Site $site
     * @return string
     */
    public function getSiteClass(Site $site)
    {
        return 'App\Services\DeploymentServices\\'.$site->getSiteLanguage();
    }

    /**
     * Gets the frameworks class
     * @param Site $site
     * @return string
     */
    public function getFrameworkClass(Site $site)
    {
        return 'App\Services\DeploymentServices\\'.$site->getFrameworkClass();
    }

    /**
     * Saves the default steps suggested for their language and framework
     * @param Site $site
     */
    public function saveDefaultSteps(Site $site)
    {
        return $this->saveNewSteps(
            $site,
            collect($this->buildDeploymentOptions($this->getDeploymentClasses($site))->values()->all())
        );
    }

    /**
     * Saves new steps to their site deployments
     *
     * @param Site $site
     * @param $newDeploymentSteps
     */
    public function saveNewSteps(Site $site, $newDeploymentSteps)
    {
        $this->deploymentSteps = $this->buildDeploymentOptions($this->getDeploymentClasses($site))->keyBy('internal_deployment_function');

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
    }

    /**
     * Gest all the deployment options
     * @param array $classes
     * @return Collection
     */
    public function buildDeploymentOptions(array $classes)
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

                    $order = $this->getDocParam($method, 'order');

                    if (!empty($order)) {

                        $description = $this->getDocParam($method, 'description');

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
     * Gets a single deployment step
     *
     * @param $deploymentStep
     * @return null
     */
    public function getDeploymentStep($deploymentStep)
    {
        return ! empty($deploymentStep['internal_deployment_function']) ? $this->deploymentSteps->get($deploymentStep['internal_deployment_function']) : null;
    }
}
