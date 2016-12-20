<?php

namespace App\Traits;

use ReflectionClass;
use Illuminate\Support\Facades\File;

trait SystemFiles
{
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
                if (is_array($classFile)) {
                    $classFile = array_map(function ($file) use ($path) {
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
        $features = collect();
        $required = [];

        if ($reflection->hasProperty('required')) {
            $required = $reflection->getProperty('required')->getValue();
        }

        $features->put($reflection->getShortName(), collect());

        foreach ($reflection->getMethods() as $method) {
            if (str_contains($method->name, 'install')) {
                $parameters = [];

                foreach ($method->getParameters() as $parameter) {
                    $parameters[$parameter->name] = $parameter->isOptional() ? $parameter->getDefaultValue() : null;
                }

                $options = $this->getDocParam($method, 'options');
                if(!empty($options)) {
                    $options = explode(',', $options);
                }

                $name = str_replace('install', '', $method->name);

                $features->get($reflection->getShortName())->put($name, collect([
                    'name' => $name,
                    'required' => in_array($method->name, $required),
                    'parameters' => $parameters,
                    'service' => str_replace('App\Services\Systems\Ubuntu\V_16_04\\', '', $reflection->getName()),
                    'description' => $this->getDocParam($method, 'description'),
                    'options' => $options,
                    'multiple' => $this->getDocParam($method, 'multiple', false)
                ]));
            }
        }

        return $features;
    }

    public function getDocParam($method, $param, $default = null)
    {
        preg_match('/\@'.$param.'\s(.*)/', $method->getDocComment(), $matches);

        if (isset($matches[1])) {
            return $matches[1];
        }

        return $default;
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
