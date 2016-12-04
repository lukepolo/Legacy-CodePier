<?php

namespace App\Traits;

use ReflectionClass;
use Illuminate\Support\Facades\File;

trait SystemFiles {
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

                if(is_array($classFile)) {
                    $classFile = array_map(function($file) use($path) {
                        return $path.$file;
                    }, $classFile);
                } else {
                    $classFile = $path.$classFile;
                }
                $files[$index] = $classFile;
            }
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