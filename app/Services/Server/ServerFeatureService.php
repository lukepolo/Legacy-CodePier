<?php

namespace App\Services\Server;

use App\Traits\SystemFiles;
use App\Models\Server\Server;
use Illuminate\Support\Facades\Cache;
use App\Contracts\Server\ServerFeatureServiceContract;

/**
 * Class ServerFeatureService.
 */
class ServerFeatureService implements ServerFeatureServiceContract
{
    use SystemFiles;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAllFeatures()
    {
        return collect(array_merge_recursive(
            $this->getBaseFeatures()->toArray(),
            $this->getLanguages()->toArray(),
            $this->getFrameworks()->toArray()
        ));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getBaseFeatures()
    {
        return Cache::rememberForever('availableFeatures', function () {
            $availableFeatures = collect();

            foreach ($this->getSystemsFiles() as $system) {
                foreach ($this->getVersionsFromSystem($system) as $version) {
                    foreach ($this->getServicesFromVersion($version) as $service) {
                        $availableFeatures = $availableFeatures->merge(
                            $this->buildFeatureArray(
                                $this->buildReflection($service)
                            )
                        );
                    }
                }
            }

            return $availableFeatures;
        });
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getLanguages()
    {
        return Cache::rememberForever('availableLanguages', function () {
            $availableLanguages = collect();

            foreach ($this->getSystemsFiles() as $system) {
                foreach ($this->getVersionsFromSystem($system) as $version) {
                    foreach ($this->getLanguagesFromVersion($version) as $language) {
                        $language .= '/'.basename($language).'.php';
                        $availableLanguages = $availableLanguages->merge(
                            $this->buildFeatureArray(
                                $this->buildReflection($language)
                            )
                        );
                    }
                }
            }

            return $availableLanguages;
        });
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getFrameworks()
    {
        return Cache::rememberForever('availableFrameworks', function () {
            $availableFrameworks = collect();

            foreach ($this->getSystemsFiles() as $system) {
                foreach ($this->getVersionsFromSystem($system) as $version) {
                    foreach ($this->getLanguagesFromVersion($version) as $language) {
                        $languageIndex = substr($language, strrpos($language, '/') + 1);

                        $availableFrameworks[$languageIndex] = collect();

                        foreach ($this->getFrameworksFromLanguage($language) as $framework) {
                            $availableFrameworks[$languageIndex] = $availableFrameworks[$languageIndex]->merge($this->buildFeatureArray(
                                $this->buildReflection($framework)
                            ));
                        }
                    }
                }
            }

            return collect($availableFrameworks);
        });
    }

    /**
     * @param Server $server
     * @return \Illuminate\Support\Collection
     */
    public function getEditableFiles(Server $server)
    {
        $files = $this->getAvailableEditableFiles();

        $editableFiles = collect();

        foreach ($server->server_features as $service => $features) {
            if (isset($server->server_features[$service])) {
                foreach ($features as $feature => $params) {
                    if ($files->has($feature)) {
                        $editableFiles = $editableFiles->merge(
                            $this->checkIfNeedsVersion($files->get($feature), $server->server_features[$service][$feature]['parameters'])
                        );
                    }
                }
            }
        }

        return $editableFiles;
    }
}
