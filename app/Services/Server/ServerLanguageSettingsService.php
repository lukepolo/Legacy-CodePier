<?php

namespace App\Services\Server;

use App\Traits\SystemFiles;
use App\Models\Server\Server;
use App\Contracts\Server\ServerLanguageSettingsServiceContract;
use App\Contracts\Server\ServerFeatureServiceContract as ServerFeatureService;

class ServerLanguageSettingsService implements ServerLanguageSettingsServiceContract
{
    use SystemFiles;

    private $serverFeatureService;

    /**
     * SiteFeatureService constructor.
     * @param \App\Services\Server\ServerFeatureService |  ServerFeatureService $serverFeatureService
     */
    public function __construct(ServerFeatureService $serverFeatureService)
    {
        $this->serverFeatureService = $serverFeatureService;
    }

    /**
     * @param Server $server
     * @return \Illuminate\Support\Collection
     */
    public function getLanguageSettings(Server $server)
    {
        $languageSettings = [];

        foreach ($server->getLanguages() as $language => $class) {
            // TODO - so how should we handle things like this, i think i was just assuming before hand
            // and looped through stuff, but seems pointless as some version may not have
            // the same settings as the others
            $reflectionClass = $this->buildReflection(
                $this->getLanguageFile('Ubuntu', 'V_16_04', $language, $language.'Settings')
            );


            $traitMethods = collect();
            foreach($reflectionClass->getTraits() as $trait) {
                foreach($trait->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                    $traitMethods->push($method->getName());
                }
            }

            foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if (!$traitMethods->contains($method->name)) {
                    $languageSettings[$language][] = [
                        'type' => $language,
                        'name' => $method->getName(),
                        'params' => $this->getDocParam($method, 'params'),
                        'description' => $this->getFirstDocParam($method, 'description'),
                    ];
                }
            }
        }

        return collect($languageSettings);
    }
}
