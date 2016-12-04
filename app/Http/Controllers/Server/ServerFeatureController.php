<?php

namespace App\Http\Controllers\Server;

use ReflectionClass;
use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Jobs\InstallServerFeature;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\Server\ServerFeatureRequest;

class ServerFeatureController extends Controller
{
    /**
     * @param ServerFeatureRequest $request
     * @param $serverId
     * @return mixed
     */
    public function store(ServerFeatureRequest $request, $serverId)
    {
        return $this->dispatch(
            new InstallServerFeature(
                Server::findOrFail($serverId),
                $request->get('feature'),
                $request->get('service'),
                $request->get('parameters', [])
            )
        );
    }

    /**
     * @return \Illuminate\Support\Collection|static
     */
    public function getServerFeatures()
    {
        $availableFeatures = collect();

        foreach ($this->getSystemsFiles() as $system) {
            foreach ($this->getVersionsFromSystem($system) as $version) {
                foreach ($this->getServicesFromVersion($version) as $service) {
                    $availableFeatures = $availableFeatures->merge($this->buildFeatureArray($this->buildReflection($service)));
                }
            }
        }

        return $availableFeatures;
    }

    /**
     * @return \Illuminate\Support\Collection|static
     */
    public function getLanguages()
    {
        $availableLanguages = collect();

        foreach ($this->getSystemsFiles() as $system) {
            foreach ($this->getVersionsFromSystem($system) as $version) {
                foreach ($this->getLanguagesFromVersion($version) as $language) {
                    $language .= '/'.basename($language).'.php';
                    $availableLanguages = $availableLanguages->merge($this->buildFeatureArray($this->buildReflection($language)));
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
                        $availableFrameworks[substr($language, strrpos($language,
                                '/') + 1)] = $this->buildFeatureArray($this->buildReflection($framework));
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
    public function getEditableServerFiles($serverId)
    {
        $editableFiles = collect();

        $server = Server::findOrFail($serverId);

        $files = collect();

        foreach ($this->getSystemsFiles() as $system) {
            foreach ($this->getVersionsFromSystem($system) as $version) {
                foreach ($this->getServicesFromVersion($version) as $service) {
                    $files = $files->merge($this->buildFileArray($this->buildReflection($service)));
                }

                foreach ($this->getLanguagesFromVersion($version) as $language) {
                    $files = $files->merge(
                        $this->buildFileArray(
                            $this->buildReflection(
                                $language.'/'.basename($language).'.php'
                            ),
                            'Languages\\'.basename($language).'\\'
                        )
                    );
                }
            }
        }

        foreach ($server->server_features as $service => $feature) {
            if (isset($server->server_features[$service])) {
                if ($files->has($service)) {
                    $editableFiles = $editableFiles->merge($files->get($service));
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
            $languageAndframework = explode('.', $site->framework);
            if (isset($files[$languageAndframework[0]][$languageAndframework[1]])) {
                $editableFiles = $editableFiles->merge($files[$languageAndframework[0]][$languageAndframework[1]]);
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
                            $reflectionClass = $this->buildReflection($language.'/Frameworks'.str_replace(basename($language).'.', '/', $site->framework).'.php');
                            $suggestedFeatures = array_merge($suggestedFeatures, $reflectionClass->getDefaultProperties()['suggestedFeatures']);
                        }

                        break;
                    }
                }
            }
        }

        return $suggestedFeatures;
    }

    /**
     * @param $file
     * @return ReflectionClass
     */
    private function buildReflection($file)
    {
        return new ReflectionClass(
            str_replace(
                '.php',
                '',
                'App'.str_replace(
                    '/',
                    '\\',
                    str_replace(
                        app_path(),
                        '',
                        $file
                    )
                )
            )
        );
    }

    /**
     * @param ReflectionClass $reflection
     * @param null $path
     * @return array
     */
    private function buildFileArray(ReflectionClass $reflection, $path = null)
    {
        $files = [];

        if ($reflection->hasProperty('files')) {
            $classFiles = $reflection->getProperty('files')->getValue();
            foreach ($classFiles as $index => $classFile) {
                $classFiles[$index] = $path.$classFile;
            }

            $files[$reflection->getShortName()] = $classFiles;
        }

        return $files;
    }

    /**
     * @param ReflectionClass $reflection
     * @return array
     */
    private function buildFeatureArray(ReflectionClass $reflection)
    {
        $features = [];
        $required = [];

        if ($reflection->hasProperty('required')) {
            $required = $reflection->getProperty('required')->getValue();
        }

        // TODO - get description or something from comment
        foreach ($reflection->getMethods() as $method) {
            if (str_contains($method->name, 'install')) {
                $parameters = [];

                foreach ($method->getParameters() as $parameter) {
                    $parameters[$parameter->name] = $parameter->isOptional() ? $parameter->getDefaultValue() : null;
                }

                $features[$reflection->getShortName()][] = [
                    'name' => str_replace('install', '', $method->name),
                    'required' => in_array($method->name, $required),
                    'parameters' => $parameters,
                    'service' => str_replace('App\Services\Systems\Ubuntu\V_16_04\\', '', $reflection->getName()),
                    'description' => trim(preg_replace('/[^a-zA-Z0-9]/', ' ', $method->getDocComment())),
                ];
            }
        }

        return $features;
    }

    /**
     * @return mixed
     */
    private function getSystemsFiles()
    {
        return File::directories(app_path('Services/Systems'));
    }

    /**
     * @param $system
     * @return mixed
     */
    private function getVersionsFromSystem($system)
    {
        return File::directories($system);
    }

    /**
     * @param $version
     * @return mixed
     */
    private function getServicesFromVersion($version)
    {
        return File::files($version);
    }

    /**
     * @param $version
     * @return mixed
     */
    private function getLanguagesFromVersion($version)
    {
        return File::directories($version.'/Languages');
    }

    /**
     * @param $language
     * @return mixed
     */
    private function getFrameworksFromLanguage($language)
    {
        return File::files($language.'/Frameworks');
    }
}
