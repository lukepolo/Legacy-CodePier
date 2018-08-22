<?php

namespace App\Services\Site;

use ReflectionClass;
use App\Models\Site\Site;
use App\Traits\SystemFiles;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use App\Models\Site\Deployment\DeploymentStep;
use App\Services\DeploymentServices\DeployTrait;
use App\Contracts\Site\SiteDeploymentStepsServiceContract;

class SiteDeploymentStepsService implements SiteDeploymentStepsServiceContract
{
    use SystemFiles;

    private $deploymentSteps = [];

    /**
     * Gets all deployment classes for their site.
     * @param $site
     * @return string
     */
    public function getDeploymentClass(Site $site)
    {
        return $this->getSiteClass($site);
    }

    /**
     * Gest the sites class.
     * @param Site $site
     * @return string
     */
    public function getSiteClass(Site $site)
    {
        return 'App\Services\DeploymentServices\\'.$site->getSiteLanguage();
    }

    /**
     * Gets the frameworks class.
     * @param Site $site
     * @return string
     */
    public function getFrameworkClass(Site $site)
    {
        return 'App\Services\DeploymentServices\\'.$site->getFrameworkClass();
    }

    /**
     * Saves the default steps suggested for their language and framework.
     * @param Site $site
     */
    public function saveDefaultSteps(Site $site)
    {
        return $this->saveNewSteps(
            $site,
            $this->buildDeploymentOptions($this->getDeploymentClass($site), $this->getFrameworkClass($site))->filter(function ($step) {
                return $step['enabled'] == true;
            })
        );
    }

    /**
     * Saves new steps to their site deployments.
     *
     * @param Site $site
     * @param $newDeploymentSteps
     */
    public function saveNewSteps(Site $site, $newDeploymentSteps)
    {
        $order = 0;

        foreach ($newDeploymentSteps as $deploymentStep) {
            $internalStep = $this->getDeploymentStep($site, $deploymentStep);

            $deploymentStepModel = DeploymentStep::firstOrnew([
                'site_id' => $site->id,
                'step' => ! empty($internalStep) ? $internalStep['step'] : $deploymentStep['step'],
                'script' => empty($internalStep) ? $deploymentStep['script'] : null,
                'internal_deployment_function' => ! empty($internalStep) ? $internalStep['internal_deployment_function'] : null,
            ]);

            $deploymentStepModel->fill([
                'server_ids' => isset($deploymentStep['server_ids']) ? $deploymentStep['server_ids'] : [],
                'server_types' => isset($deploymentStep['server_types']) ? $deploymentStep['server_types'] : [],
                'after_deploy' =>  isset($deploymentStep['after_deploy']) && $deploymentStep['after_deploy'] ? true : false,
            ]);

            $deploymentStepModel->order = ++$order;
            $deploymentStepModel->save();
        }
    }

    /**
     * Gets all the deployment options.
     * @param $class
     * @param null $frameworkClass
     * @return Collection
     */
    public function buildDeploymentOptions($class, $frameworkClass = null)
    {
        return Cache::tags('app.services')->rememberForever("deploymentOptions.$class.$frameworkClass", function () use ($class, $frameworkClass) {
            $deploymentSteps = [];

            $reflection = new ReflectionClass($class);

            $traitMethods = collect();

            foreach ($reflection->getTraits() as $reflectionTraitClass) {
                if ($reflectionTraitClass->name != $frameworkClass && $reflectionTraitClass->name != DeployTrait::class) {
                    foreach ($reflectionTraitClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                        $traitMethods->push($method->name);
                    }
                }
            }

            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->name != '__construct' && ! $traitMethods->contains($method->name)) {
                    $order = $this->getFirstDocParam($method, 'order');

                    if (! empty($order)) {
                        $description = $this->getFirstDocParam($method, 'description');

                        $deploymentSteps[] = [
                               'order' => (int) $order,
                               'zero_downtime_deployment' => $this->getFirstDocParam($method, 'zero_downtime_deployment'),
                               'description' => $description,
                               'internal_deployment_function' => $method->name,
                               'step' => ucwords(str_replace('_', ' ', snake_case($method->name))),
                               'enabled' => $this->getFirstDocParam($method, 'not_default'),
                           ];
                    }
                }
            }

            return collect(collect($deploymentSteps)->sortBy('order')->values())->map(function ($deploymentStep, $index) {
                $deploymentStep['order'] = $index + 1;

                return $deploymentStep;
            });
        });
    }

    /**
     * Gets a single deployment step.
     *
     * @param Site $site
     * @param $deploymentStep
     * @return null
     */
    public function getDeploymentStep(Site $site, $deploymentStep)
    {
        if (empty($this->deploymentSteps)) {
            $this->deploymentSteps = $this->buildDeploymentOptions($this->getDeploymentClass($site), $this->getFrameworkClass($site))->keyBy('internal_deployment_function');
        }

        return ! empty($deploymentStep['internal_deployment_function']) ? $this->deploymentSteps->get($deploymentStep['internal_deployment_function']) : null;
    }
}
