<?php

namespace App\Services\Server;

use App\Models\Site\Site;
use App\Traits\SystemFiles;
use App\Models\Server\Server;
use App\Contracts\Server\ServerFeatureServiceContract;

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
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getLanguages()
    {
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
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getFrameworks()
    {
        $availableFrameworks = collect();

        foreach ($this->getSystemsFiles() as $system) {
            foreach ($this->getVersionsFromSystem($system) as $version) {
                foreach ($this->getLanguagesFromVersion($version) as $language) {
                    foreach ($this->getFrameworksFromLanguage($language) as $framework) {
                        $availableFrameworks[substr($language, strrpos($language, '/') + 1)] = $this->buildFeatureArray(
                            $this->buildReflection($framework)
                        );
                    }
                }
            }
        }

        return $availableFrameworks;
    }

    /**
     * @param Server $server
     * @return \Illuminate\Support\Collection
     */
    public function getEditableFiles(Server $server)
    {
        $editableFiles = collect();

        $files = collect();

        foreach ($this->getSystemsFiles() as $system) {
            foreach ($this->getVersionsFromSystem($system) as $version) {
                foreach ($this->getServicesFromVersion($version) as $service) {
                    $files = $files->merge(
                        $this->buildFileArray(
                            $this->buildReflection($service)
                        )
                    );
                }

                foreach ($this->getLanguagesFromVersion($version) as $language) {
                    $files = $files->merge(
                        $this->buildFileArray(
                            $this->buildReflection($language.'/'.basename($language).'.php')
                        )
                    );
                }
            }
        }

        foreach ($server->server_features as $service => $features) {
            if (isset($server->server_features[$service])) {
                foreach ($features as $feature => $params) {
                    if ($files->has($feature)) {
                        $editableFiles = $editableFiles->merge($files->get($feature));
                    }
                }
            }
        }

        return $editableFiles;
    }

    /**
     * @param Site $site
     * @return \Illuminate\Support\Collection
     */
    public function getEditableFrameworkFiles(Site $site)
    {
        $editableFiles = collect();

        $files = [];

        foreach ($this->getSystemsFiles() as $system) {
            foreach ($this->getVersionsFromSystem($system) as $version) {
                foreach ($this->getLanguagesFromVersion($version) as $language) {
                    foreach ($this->getFrameworksFromLanguage($language) as $framework) {
                        $language = substr($language, strrpos($language, '/') + 1);
                        $reflectionClass = $this->buildReflection($framework);
                        $files[$language] = $this->buildFileArray($reflectionClass, $site->path.'/');
                    }
                }
            }
        }

        if (! empty($site->framework)) {
            $languageAndFramework = explode('.', $site->framework);

            if (isset($files[$languageAndFramework[0]][$languageAndFramework[1]])) {
                $editableFiles = $editableFiles->merge($files[$languageAndFramework[0]][$languageAndFramework[1]]);
            }
        }

        return $editableFiles;
    }
}
