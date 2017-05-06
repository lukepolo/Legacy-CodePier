<?php

namespace App\Http\Controllers\Server;

use App\Models\Server\Server;
use App\Models\LanguageSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageSettingRequest;
use App\Jobs\Server\UpdateServerLanguageSetting;
use App\Contracts\Site\ServerLanguageSettingsServiceContract as ServerLanguageSettingsService;

class ServerLanguageSettingsController extends Controller
{
    private $serverLanguageSettingsService;

    /**
     * SiteFeatureController constructor.
     * @param \App\Services\Server\ServerLanguageSettingsService | ServerLanguageSettingsService $serverLanguageSettingsService
     */
    public function __construct(ServerLanguageSettingsService $serverLanguageSettingsService)
    {
        $this->serverLanguageSettingsService = $serverLanguageSettingsService;
    }

    /**
     * Display a listing of the resource.
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Server::findOrFail($siteId)->languageSettings
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LanguageSettingRequest $request
     * @param  int $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(LanguageSettingRequest $request, $serverId)
    {
        $server = Server::with('languageSettings')->findOrFail($serverId);

        $languageSetting = LanguageSetting::create([
            'params' => $request->get('params'),
            'setting' => $request->get('setting'),
            'language' => $request->get('language'),
        ]);

        $server->languageSettings()->save($languageSetting);

        dispatch(
            (new UpdateServerLanguageSetting($server, $languageSetting))->onQueue(config('queue.channels.server_commands'))
        );

        return response()->json($languageSetting);
    }

    /**
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLanguageSettings($serverId)
    {
        return response()->json(
            $this->serverLanguageSettingsService->getLanguageSettings(
                Server::findOrFail($serverId)
            )
        );
    }
}
