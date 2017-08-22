<?php

namespace App\Http\Controllers\Server;

use App\Models\Server\Server;
use App\Models\LanguageSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageSettingRequest;
use App\Jobs\Server\UpdateServerLanguageSetting;
use App\Contracts\Server\ServerLanguageSettingsServiceContract as ServerLanguageSettingsService;

class ServerLanguageSettingsController extends Controller
{
    private $serverLanguageSettingsService;

    /**
     * ServerFeatureController constructor.
     * @param \App\Services\Server\ServerLanguageSettingsService | ServerLanguageSettingsService $serverLanguageSettingsService
     */
    public function __construct(ServerLanguageSettingsService $serverLanguageSettingsService)
    {
        $this->serverLanguageSettingsService = $serverLanguageSettingsService;
    }

    /**
     * Display a listing of the resource.
     * @param  int $serverId
     * @return \Illuminate\Http\Response
     */
    public function index($serverId)
    {
        return response()->json(
            Server::findOrFail($serverId)->languageSettings
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

        $setting = $request->get('setting');
        $language = $request->get('language');

        $languageSetting = $server->languageSettings
            ->where('setting', $setting)
            ->where('language', $language)
            ->first();

        if (empty($languageSetting)) {
            $languageSetting = LanguageSetting::create([
                'setting' => $setting,
                'language' => $language,
            ]);

            $server->languageSettings()->save($languageSetting);
        }

        $languageSetting->update([
            'params' => $request->get('params', []),
        ]);

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
