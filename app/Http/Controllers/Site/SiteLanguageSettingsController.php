<?php

namespace App\Http\Controllers\Site;

use App\Models\LanguageSetting;
use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageSettingRequest;
use App\Events\Site\SiteLanguageSettingUpdated;

class SiteLanguageSettingsController extends Controller
{
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
            'data' => $request->get('data'),
            'class' => $request->get('class'),
            'function' => $request->get('function'),
        ]);

        $site->languageSetting()->save($languageSetting);

        broadcast(new SiteLanguageSettingUpdated($site, $languageSetting))->toOthers();

        return response()->json($languageSetting);
    }

    public function getLanguageSettings($siteId)
    {


        return response()->json('SITE LANG SETTINGS');
    }
}
