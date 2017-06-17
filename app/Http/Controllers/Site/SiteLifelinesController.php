<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Models\Site\Lifeline;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteLifeline;

class SiteLifelinesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(Site::findOrFail($siteId)->lifeLines);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SiteLifeline $request
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(SiteLifeline $request, $siteId)
    {
        return response()->json(
            Lifeline::create([
                'name' => $request->get('name'),
                'site_id' =>  Site::findOrFail($siteId)->id,
            ])
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId, $id)
    {
        $site = Site::findOrFail($siteId);

        $site->lifeLines->keyBy('id')->get($id)->delete();

        return response()->json('OK');
    }
}
