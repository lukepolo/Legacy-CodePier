<?php

namespace App\Services\Site;

use App\Models\Site\Site;
use App\Traits\SystemFiles;
use App\Contracts\Site\SiteFeatureServiceContract;
use App\Contracts\Server\ServerFeatureServiceContract as ServerFeatureService;

class SiteFeatureService implements SiteFeatureServiceContract
{
    use SystemFiles;

    private $serverFeatureService;

    /**
     * SiteFeatureService constructor.
     * @param \App\Services\Server\ServerFeatureService |  ServerFeatureService $serverFeatureService
     */
    public function __construct(ServerFeatureService $serverFeatureService)
    {
        $this->serverFeatureService = $serverFeatureService;
    }

    /**
     * @param Site $site
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEditableFrameworkFiles(Site $site)
    {
        $editableFiles = collect();

        if (! empty($site->framework)) {
            $files = [];

            foreach ($this->getSystemsFiles() as $system) {
                foreach ($this->getVersionsFromSystem($system) as $version) {
                    foreach ($this->getLanguagesFromVersion($version) as $language) {
                        foreach ($this->getFrameworksFromLanguage($language) as $framework) {
                            $language = substr($language, strrpos($language, '/') + 1);
                            $reflectionClass = $this->buildReflection($framework);
                            $files[$language][$reflectionClass->getShortName()] = $this->buildFileArray($reflectionClass, $site->path.'/');
                        }
                    }
                }
            }

            $languageAndFramework = explode('.', $site->framework);

            if (isset($files[$languageAndFramework[0]][$languageAndFramework[1]])) {
                $editableFiles = $editableFiles->merge($files[$languageAndFramework[0]][$languageAndFramework[1]]);
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
        $suggestedFeatures = [];

        $possibleFeatures = $this->serverFeatureService->getAllFeatures();

        foreach ($this->getSystemsFiles() as $system) {
            foreach ($this->getVersionsFromSystem($system) as $version) {
                foreach ($this->getLanguagesFromVersion($version) as $language) {
                    if (strtolower(basename($language)) == strtolower($site->type)) {
                        $reflectionClass = $this->buildReflection($language.'/'.basename($language).'.php');

                        $tempSuggestedFeatures = $reflectionClass->getDefaultProperties()['suggestedFeatures'];

                        if (! empty($site->framework)) {
                            $reflectionClass = $this->buildReflection($language.'/Frameworks'.str_replace(basename($language).'.', '/', $site->framework).'.php');
                            $tempSuggestedFeatures = array_merge($tempSuggestedFeatures, $reflectionClass->getDefaultProperties()['suggestedFeatures']);
                        }

                        foreach ($tempSuggestedFeatures as $service => $serverSuggestedFeatures) {

                            if (! isset($suggestedFeatures[$service])) {
                                $suggestedFeatures[$service] = [];
                            }

                            foreach ($serverSuggestedFeatures as $suggestedFeature) {
                                $features = $possibleFeatures;

                                if (strpos($service, '.') > 0) {
                                    $serviceParts = explode('.', $service);
                                    $service = array_pop($serviceParts);

                                    foreach ($serviceParts as $part) {

                                    }

                                    $suggestedFeatures[$service][$suggestedFeature] = $features->get($service)[$suggestedFeature];
                                    $suggestedFeatures[$service][$suggestedFeature]['enabled'] = true;

                                } else {
                                    $suggestedFeatures[$service][$suggestedFeature] = $features->get($service)[$suggestedFeature];
                                    $suggestedFeatures[$service][$suggestedFeature]['enabled'] = true;
                                }
                            }
                        }

                        break;
                    }
                }
            }
        }

        return $suggestedFeatures;
    }

    /**
     * Saves the suggested defaults to their site.
     * @param Site $site
     * @return mixed
     */
    public function saveSuggestedDefaults(Site $site)
    {
        return $site->update([
            'server_features' => $this->getSuggestedFeatures($site),
        ]);
    }
}
