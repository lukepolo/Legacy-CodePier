<?php

namespace App\Services\Site;

use App\Models\CronJob;
use App\Models\Site\Site;
use App\Traits\SystemFiles;
use Illuminate\Support\Facades\Cache;
use App\Contracts\Site\SiteFeatureServiceContract;
use App\Contracts\Repositories\FileRepositoryContract as FileRepository;
use App\Contracts\Server\ServerFeatureServiceContract as ServerFeatureService;

class SiteFeatureService implements SiteFeatureServiceContract
{
    use SystemFiles;

    private $fileRepository;
    private $serverFeatureService;

    /**
     * SiteFeatureService constructor.
     * @param \App\Services\Server\ServerFeatureService | ServerFeatureService $serverFeatureService
     * @param \App\Repositories\FileRepository | FileRepository $fileRepository
     */
    public function __construct(ServerFeatureService $serverFeatureService, FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
        $this->serverFeatureService = $serverFeatureService;
    }

    /**
     * @param Site $site
     * @return \Illuminate\Support\Collection
     */
    public function getEditableFiles(Site $site)
    {
        $files = $this->getAvailableEditableFiles();

        $editableFiles = collect();

        if (! empty($site->server_features)) {
            foreach ($site->server_features as $service => $features) {
                if (isset($site->server_features[$service])) {
                    foreach ($features as $feature => $params) {
                        if ($files->has($feature)) {
                            $editableFiles = $editableFiles->merge(
                                $this->checkIfNeedsVersion($files->get($feature), $site->server_features[$service][$feature]['parameters'])
                            );
                        }
                    }
                }
            }
        }

        return $editableFiles;
    }

    /**
     * @param Site $site
     * @return \Illuminate\Support\Collection|static
     */
    public function getEditableFrameworkFiles(Site $site)
    {
        $editableFiles = collect();

        // TODO - we need to make this cacheable , and do a foreach on the path isntead
        if (! empty($site->framework)) {
            $files = [];

            foreach ($this->getSystemsFiles() as $system) {
                foreach ($this->getVersionsFromSystem($system) as $version) {
                    foreach ($this->getLanguagesFromVersion($version) as $language) {
                        foreach ($this->getFrameworksFromLanguage($language) as $framework) {
                            $tempLanguage = substr($language, strrpos($language, '/') + 1);
                            $reflectionClass = $this->buildReflection($framework);
                            $files[$tempLanguage][$reflectionClass->getShortName()] = $this->buildFileArray($reflectionClass, $site->path.'/');
                        }
                    }
                }
            }

            if (isset($files[$site->type][$site->framework])) {
                $editableFiles = $editableFiles->merge($files[$site->type][$site->framework]);
            }
        }

        return $editableFiles;
    }

    /**
     * @param Site $site
     * @return array
     * @internal param $siteId
     */
    public function getSuggestedFeatures(Site $site)
    {
        return Cache::tags('app.services')->rememberForever("suggestedFeatures.$site->site_type", function () use ($site) {
            $suggestedFeatures = [];

            foreach ($this->getSystemsFiles() as $system) {
                foreach ($this->getVersionsFromSystem($system) as $version) {
                    foreach ($this->getLanguagesFromVersion($version) as $language) {
                        if (strtolower(basename($language)) == strtolower($site->type)) {
                            $reflectionClass = $this->buildReflection($language.'/'.basename($language).'.php');

                            $siteSuggestedFeatures = $reflectionClass->getDefaultProperties()['suggestedFeatures'];

                            if (! empty($site->framework)) {
                                $reflectionClass = $this->buildReflection("$language/Frameworks/$site->framework.php");
                                $siteSuggestedFeatures = array_merge($siteSuggestedFeatures, $reflectionClass->getDefaultProperties()['suggestedFeatures']);
                            }

                            $servicePath = '\Services\Systems\Ubuntu\V_16_04\\';

                            foreach ($siteSuggestedFeatures as $service => $features) {
                                $reflectionClass = $this->buildReflection($servicePath.$service.'.php');
                                foreach ($features as $feature) {
                                    $method = $reflectionClass->getMethod('install'.$feature);

                                    $parameters = [];
                                    foreach ($method->getParameters() as $parameter) {
                                        $parameters[$parameter->name] = $parameter->isOptional() ? $parameter->getDefaultValue() : null;
                                    }

                                    $suggestedFeatures[$service][$feature] = array_filter([
                                        'enabled' => true,
                                        'parameters' => $parameters,
                                    ]);
                                }
                            }

                            break;
                        }
                    }
                }
            }

            return collect($suggestedFeatures);
        });
    }

    /**
     * Saves the suggested defaults to their site.
     * @param Site $site
     * @return $site
     */
    public function getSuggestedFeaturesDefaults(Site $site)
    {
        return $this->getSuggestedFeatures($site);
    }

    /**
     * @param Site $site
     * @return \Illuminate\Support\Collection|static
     */
    public function getSuggestedCronJobs(Site $site)
    {
        // TODO - we need to move suggestions out of the systems , cause its not going to end well
        if ($site->framework) {
            $reflectionClass = $this->buildReflection('\Services\Systems\Ubuntu\V_16_04\Languages\\'.$site->getFrameworkClass().'.php');

            return collect($reflectionClass->getDefaultProperties()['cronJobs'])->map(function ($cronJob) use ($site) {
                $path = '/home/codepier/'.$site->domain;

                if ($site->zero_downtime_deployment) {
                    $path .= '/current';
                }

                return str_replace('{site_path}', $path, $cronJob);
            });
        }

        return collect();
    }

    /**
     * @param Site $site
     */
    public function detachSuggestedCronJobs(Site $site)
    {
        foreach ($site->cronJobs->where('framework_cronjob', 1) as $siteCronJob) {
            $site->cronJobs()->detach($siteCronJob);
        }
    }

    /**
     * @param Site $site
     */
    public function saveSuggestedFiles(Site $site)
    {
        foreach ($this->getEditableFiles($site) as $file) {
            $this->fileRepository->findOrCreateFile($site, $file);
        }

        foreach ($this->getEditableFrameworkFiles($site) as $file) {
            $this->fileRepository->findOrCreateFile($site, $file, false, true);
        }
    }

    /**
     * @param Site $site
     * @param bool $framework
     */
    public function detachSuggestedFiles(Site $site, $framework = false)
    {
        foreach ($site->files->where('custom', 0)->where('framework_file', $framework) as $file) {
            $site->files()->detach($file);
            if ($file->servers->count() === 0) {
                $file->delete();
            }
        }
    }

    /**
     * @param Site $site
     */
    public function saveSuggestedCronJobs(Site $site)
    {
        foreach ($this->getSuggestedCronJobs($site) as $cronJob) {
            $cronJobModel = CronJob::create([
                'job' => $cronJob,
                'user' => 'codepier',
                'framework_cronjob' => true,
            ]);

            $site->cronJobs()->save($cronJobModel);
        }
    }
}
