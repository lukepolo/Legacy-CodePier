<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use ReflectionClass;

/**
 * Class ServerFeatureController.
 */
class ServerFeatureController extends Controller
{
    public function getServerFeatures()
    {
        $availableFeatures = collect();

        $systems = File::directories(app_path('Services/Systems'));

        foreach ($systems as $system) {
            $versions = File::directories($system);
            foreach ($versions as $version) {
                $services = File::files($version);
                foreach ($services as $service) {
                    $availableFeatures = $availableFeatures->merge($this->buildFeatureArray($this->buildReflection($service)));
                }
            }
        }

        return $availableFeatures;
    }

    public function getLanguages()
    {
        $availableLanguages = collect();

        $systems = File::directories(app_path('Services/Systems'));
        foreach ($systems as $system) {
            $versions = File::directories($system);
            foreach ($versions as $version) {
                $languages = File::directories($version.'/Languages');

                foreach ($languages as $language) {
                    $language .= '/'.basename($language).'.php';
                    $availableLanguages = $availableLanguages->merge($this->buildFeatureArray($this->buildReflection($language)));
                }
            }
        }

        return $availableLanguages;
    }

    public function getFrameworks()
    {
        $availableFrameworks = [];

        $systems = File::directories(app_path('Services/Systems'));
        foreach ($systems as $system) {
            $versions = File::directories($system);
            foreach ($versions as $version) {
                $languages = File::directories($version.'/Languages');

                foreach ($languages as $language) {
                    foreach (File::files($language.'/Frameworks') as $framework) {
                        $availableFrameworks[basename($language)] = $this->buildFeatureArray($this->buildReflection($framework));
                    }
                }
            }
        }

        return $availableFrameworks;
    }

    private function buildReflection($file)
    {
        return new ReflectionClass(str_replace('.php', '', 'App'.str_replace('/', '\\', str_replace(app_path(), '', $file))));
    }

    private function buildFeatureArray(ReflectionClass $reflection)
    {
        $features = [];
        $required = [];

        if ($reflection->hasProperty('required')) {
            $required = $reflection->getProperty('required')->getValue();
        }

        foreach ($reflection->getMethods() as $method) {
            if (str_contains($method->name, 'install')) {
                $parameters = [];

                foreach ($method->getParameters() as $parameter) {
                    $parameters[] = $parameter->name;
                }

                $features[$reflection->getShortName()][] = [
                    'feature' => str_replace('install', '', $method->name),
                    'parameters' => $parameters,
                    'required' => in_array($method->name, $required),
                ];
            }
        }

        return $features;
    }
}
