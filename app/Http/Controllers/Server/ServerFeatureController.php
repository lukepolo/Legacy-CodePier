<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ReflectionClass;

/**
 * Class ServerFeatureController.
 */
class ServerFeatureController extends Controller
{
    private $serverService;

    /**
     * ServerController constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    public function store(Request $request, $serverId)
    {
        $server = Server::findOrFail($serverId);
        $feature = $request->get('feature');
        $service = $request->get('service');
        $parameters = $request->get('parameters', []);

        $this->runOnServer($server, function () use ($server, $feature, $service, $parameters) {
            call_user_func_array([$this->serverService->getService($service, $server), 'install'.$feature], $parameters);

            $serverFeatures = $server->server_features;
            $serverFeatures[$service][$feature]['enabled'] = true;

            $server->server_features = $serverFeatures;
            $server->save();
        });

        return $this->remoteResponse();
    }

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

    public function getFrameworks()
    {
        $availableFrameworks = collect();

        foreach ($this->getSystemsFiles() as $system) {
            foreach ($this->getVersionsFromSystem($system) as $version) {
                foreach ($this->getLanguagesFromVersion($version) as $language) {
                    foreach ($this->getFrameworksFromLanguage($language) as $framework) {
                        $availableFrameworks = $availableFrameworks->merge($this->buildFeatureArray($this->buildReflection($framework)));
                    }
                }
            }
        }

        return $availableFrameworks;
    }

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
                    $language .= '/'.basename($language).'.php';
                    $files = $files->merge($this->buildFileArray($this->buildReflection($language)));
                }
            }
        }

        foreach($server->server_features as $service => $feature) {
            if(isset($server->server_features[$service])) {
                if($files->has($service)) {
                    $editableFiles = $editableFiles->merge($files->get($service));
                }
            }
        }

        return response()->json($editableFiles);
    }

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

    private function buildFileArray(ReflectionClass $reflection)
    {
        $files = [];

        if ($reflection->hasProperty('files')) {
            $files[str_replace('App\Services\Systems\Ubuntu\V_16_04\\', '', $reflection->getName())] = $reflection->getProperty('files')->getValue();
        }

        return $files;
    }

    private function buildFeatureArray(ReflectionClass $reflection)
    {
        $features = [];
        $required = [];
        $suggestedDefaults = [];

        if ($reflection->hasProperty('required')) {
            $required = $reflection->getProperty('required')->getValue();
        }

        if ($reflection->hasProperty('suggestedDefaults')) {
            $suggestedDefaults = $reflection->getProperty('suggestedDefaults')->getValue();
        }

        // TODO - get description or something from comment
//        $reflection->getDocComment()
        foreach ($reflection->getMethods() as $method) {
            if (str_contains($method->name, 'install')) {
                $parameters = [];

                foreach ($method->getParameters() as $parameter) {
                    $parameters[$parameter->name] = $parameter->isOptional() ? $parameter->getDefaultValue() : null;
                }

                $features[str_replace('App\Services\Systems\Ubuntu\V_16_04\\', '', $reflection->getName())][] = [
                    'name' => str_replace('install', '', $method->name),
                    'required' => in_array($method->name, $required),
                    'parameters' => $parameters,
                    'suggestedDefaults' => $suggestedDefaults,
                ];
            }
        }

        return $features;
    }

    private function getSystemsFiles()
    {
        return File::directories(app_path('Services/Systems'));
    }

    private function getVersionsFromSystem($system)
    {
        return File::directories($system);
    }

    private function getServicesFromVersion($version)
    {
        return File::files($version);
    }

    private function getLanguagesFromVersion($version)
    {
        return File::directories($version.'/Languages');
    }

    private function getFrameworksFromLanguage($language)
    {
        return File::files($language.'/Frameworks');
    }
}
