<?php

namespace App\Http\Controllers\Server;

use App\Models\Site\Site;
use App\Traits\SystemFiles;
use App\Models\Server\Server;
use App\Jobs\InstallServerFeature;
use App\Http\Controllers\Controller;
use App\Http\Requests\Server\ServerFeatureRequest;

class ServerFeatureController extends Controller
{
    use SystemFiles;

    /**
     * @param ServerFeatureRequest $request
     * @param $serverId
     * @return mixed
     */
    public function store(ServerFeatureRequest $request, $serverId)
    {
        return $this->dispatch(
            (new InstallServerFeature(
                Server::findOrFail($serverId),
                $request->get('feature'),
                $request->get('service'),
                $request->get('parameters', [])
            ))->onQueue(env('SERVER_FEATURE_QUEUE'))
        );
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getFeatures()
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
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEditableFiles($serverId)
    {
        $editableFiles = collect();

        $server = Server::findOrFail($serverId);

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

        return response()->json($editableFiles);
    }

    /**
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEditableFrameworkFiles($siteId)
    {
        $editableFiles = collect();

        $site = Site::findOrFail($siteId);

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

        return response()->json($editableFiles);
    }

    /**
     * @param $siteId
     * @return array
     */
    public function getSuggestedFeatures($siteId)
    {
        $site = Site::findOrFail($siteId);
        $suggestedFeatures = [];

        foreach ($this->getSystemsFiles() as $system) {
            foreach ($this->getVersionsFromSystem($system) as $version) {
                foreach ($this->getLanguagesFromVersion($version) as $language) {
                    if (strtolower(basename($language)) == strtolower($site->type)) {
                        $reflectionClass = $this->buildReflection($language.'/'.basename($language).'.php');

                        $suggestedFeatures = $reflectionClass->getDefaultProperties()['suggestedFeatures'];

                        if (! empty($site->framework)) {
                            $reflectionClass = $this->buildReflection($language.'/Frameworks'.str_replace(basename($language).'.',
                                    '/', $site->framework).'.php');
                            $suggestedFeatures = array_merge($suggestedFeatures,
                                $reflectionClass->getDefaultProperties()['suggestedFeatures']);
                        }

                        break;
                    }
                }
            }
        }

        return $suggestedFeatures;
    }
}
