<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Site\SiteCronJob;
use Illuminate\Http\Request;

/**
 * Class SiteCronJobController.
 */
class SiteCronJobController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(SiteCronJob::where('site_id', $siteId)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $siteId)
    {
        return response()->json(
            SiteCronJob::create([
                'site_id' => $siteId,
                'job' => $request->get('job'),
                'user' => $request->get('user'),
            ])
        );
    }

    /**
     * Display the specified resource.
     *
     * @param int $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($siteId, $id)
    {
        return response()->json(SiteCronJob::where('site_id', $siteId)->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $siteId, $id)
    {
        $siteCronJob = SiteCronJob::where('site_id', $siteId)->findOrFail($id);

        return response()->json(
            $siteCronJob->update([
                'job' => $request->get('job'),
                'user' => $request->get('user'),
            ])
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId, $id)
    {
        return response()->json(SiteCronJob::where('site_id', $siteId)->findOrFail($id)->delete());
    }
}
