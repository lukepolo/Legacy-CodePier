<?php

namespace App\Traits;

use ReflectionClass;
use Illuminate\Support\Collection;
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
     * @param $files
     * @param $serverFeature
     * @return Collection
     */
    private function checkIfNeedsVersion($files, $serverFeature)
    {
        $files = (array) $files;

        return collect($files)->map(function ($file) use ($serverFeature) {
            if (str_contains($file, '{')) {
                if (preg_match('/{(.*)}/', $file, $matches)) {
                    return preg_replace('/{(.*)}/', $serverFeature[$matches[1]], $file);
                }
            }

            return $file;
        });
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

                $options = $this->getFirstDocParam($method, 'options');
                if (! empty($options)) {
                    $options = array_map('trim', explode(',', $options));
                }

                $inputName = str_replace('install', '', $method->name);

                $features->get($reflection->getShortName())->put($inputName, collect([
                    'name' => $this->getFirstDocParam($method, 'name', $inputName),
                    'input_name' => $inputName,
                    'required' => in_array($method->name, $required),
                    'parameters' => $parameters,
                    'service' => str_replace('App\Services\Systems\Ubuntu\V_16_04\\', '', $reflection->getName()),
                    'description' => $this->getFirstDocParam($method, 'description'),
                    'options' => $options,
                    'multiple' => $this->getFirstDocParam($method, 'multiple', false),
                    'conflicts' => array_filter(explode(',', $this->getFirstDocParam($method, 'conflicts'))),
                ]));
            }
        }

        return $features;
    }

    /**
     * @param $method
     * @param $param
     * @param null $default
     * @return null
     */
    public function getDocParam($method, $param, $default = null)
    {
        preg_match_all('/\@'.$param.'\s(.*)/', $method->getDocComment(), $matches);

        if (isset($matches[1])) {
            return $matches[1];
        }

        return $default;
    }

    /**
     * @param $method
     * @param $param
     * @param null $default
     * @return null
     */
    public function getFirstDocParam($method, $param, $default = null)
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
    private function getBuoyFiles()
    {
        return File::files(app_path('Services/Buoys'));
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
     * @param $system
     * @param $version
     * @param $language
     * @return mixed
     */
    private function getLanguageFile($system, $version, $language, $file)
    {
        return app_path("Services/Systems/$system/$version/Languages/$language/$file.php");
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
