<?php

namespace App\Services\Site;

use App\Models\Site\Site;
use App\Traits\SystemFiles;
use Illuminate\Support\Facades\Cache;
use App\Contracts\Site\SiteLanguageSettingsServiceContract;
use App\Contracts\Server\ServerFeatureServiceContract as ServerFeatureService;

class SiteLanguageSettingsService implements SiteLanguageSettingsServiceContract
{
    use SystemFiles;

    private $serverFeatureService;

    /**
     * SiteFeatureService constructor.
     *
     * @param \App\Services\Server\ServerFeatureService |  ServerFeatureService $serverFeatureService
     */
    public function __construct(ServerFeatureService $serverFeatureService)
    {
        $this->serverFeatureService = $serverFeatureService;
    }

    /**
     * @param Site $site
     *
     * @return \Illuminate\Support\Collection
     */
    public function getLanguageSettings(Site $site)
    {
        return Cache::rememberForever("languageSettings.{$site->type}", function () use ($site) {
            $languageSettings = [];

            // TODO - so how should we handle things like this, i think i was just assuming before hand
            // and looped through stuff, but seems pointless as some version may not have
            // the same settings as the others
            $reflectionClass = $this->buildReflection(
                $this->getLanguageFile('Ubuntu', 'V_16_04', $site->type, $site->type.'Settings')
            );

            $traitMethods = collect();

            foreach ($reflectionClass->getTraits() as $trait) {
                foreach ($trait->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                    $traitMethods->push($method->getName());
                }
            }

            foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $parameters = [];

                foreach ($method->getParameters() as $parameter) {
                    $parameters[$parameter->name] = $parameter->isOptional() ? $parameter->getDefaultValue() : null;
                }

                $parameterOptions = [];

                foreach ($parameters as $parameter => $value) {
                    $parameterOptions[$parameter] = json_decode($this->getFirstDocParam($method, $parameter), true);
                }

                if (! $traitMethods->contains($method->name)) {
                    $languageSettings[$site->type][] = [
                        'type' => $site->type,
                        'params' => $parameters,
                        'name' => $method->getName(),
                        'parameter_options' => $parameterOptions,
                        'description' => $this->getFirstDocParam($method, 'description'),
                    ];
                }
            }

            return collect($languageSettings);
        });
    }
}
