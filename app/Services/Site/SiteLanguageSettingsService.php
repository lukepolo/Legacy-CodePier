<?php

namespace App\Services\Site;

use App\Models\Site\Site;
use App\Traits\SystemFiles;
use App\Contracts\Site\SiteLanguageSettingsServiceContract;
use App\Contracts\Server\ServerFeatureServiceContract as ServerFeatureService;

class SiteLanguageSettingsService implements SiteLanguageSettingsServiceContract
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
     * @param Site $site
     * @return \Illuminate\Support\Collection
     */
    public function getLanguageSettings(Site $site)
    {
        $languageSettings = [];

        // TODO - so how should we handle things like this, i think i was just assuming before hand
        // and looped through stuff, but seems pointless as some version may not have
        // the same settings as the others
        $reflectionClass = $this->buildReflection(
            $this->getLanguageFile('Ubuntu', 'V_16_04', $site->type, $site->type.'Settings')
        );

        foreach ($reflectionClass->getMethods() as $method) {
            $languageSettings[] = [
                'type' => $site->type,
                'name' => $method->getName(),
                'params' => $this->getDocParam($method, 'params'),
                'description' => $this->getFirstDocParam($method, 'description'),
            ];
        }

        return collect($languageSettings);
    }
}
