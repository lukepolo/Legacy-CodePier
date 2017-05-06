<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Models\LanguageSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageSettingRequest;
use App\Events\Site\SiteLanguageSettingUpdated;
use App\Contracts\Site\SiteLanguageSettingsServiceContract as SiteLanguageSettingsService;

class SiteLanguageSettingsController extends Controller
{
    private $siteLanguageSettingsService;

    /**
     * SiteFeatureController constructor.
     * @param \App\Services\Site\SiteLanguageSettingsService | SiteLanguageSettingsService $siteLanguageSettingsService
     */
    public function __construct(SiteLanguageSettingsService $siteLanguageSettingsService)
    {
        $this->siteLanguageSettingsService = $siteLanguageSettingsService;
    }

    /**
     * Display a listing of the resource.
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Site::findOrFail($siteId)->languageSettings
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LanguageSettingRequest $request
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(LanguageSettingRequest $request, $siteId)
    {
        $site = Site::with('languageSettings')->findOrFail($siteId);

        $languageSetting = LanguageSetting::create([
            'params' => $request->get('params'),
            'setting' => $request->get('setting'),
            'language' => $request->get('language'),
        ]);

        $site->languageSettings()->save($languageSetting);

        broadcast(new SiteLanguageSettingUpdated($site, $languageSetting))->toOthers();

        return response()->json($languageSetting);
    }

    /**
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLanguageSettings($siteId)
    {
        return response()->json($this->siteLanguageSettingsService->getLanguageSettings(
            Site::findOrFail($siteId)
        ));
    }
}
