<?php

namespace App\Services\Server;

use App\Traits\SystemFiles;
use App\Models\Server\Server;
use App\Contracts\Site\ServerLanguageSettingsServiceContract;
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

        dd($server);
//        foreach($reflectionClass->getMethods() as $method) {
//            $languageSettings[] = [
//                'type' => $site->type,
//                'name' => $method->getName(),
//                'params' => $this->getDocParam($method, 'params'),
//                'description' => $this->getFirstDocParam($method, 'description'),
//            ];
//        }

        return collect($languageSettings);
    }
}
