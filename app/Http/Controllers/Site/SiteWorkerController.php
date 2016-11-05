<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Site\SiteWorker;
use Illuminate\Http\Request;

class SiteWorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $siteId)
    {
        return response()->json(
            SiteWorker::where('site_id', $siteId)->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $siteId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $siteId)
    {
        return response()->json(
            SiteWorker::create([
                'site_id' => $siteId,
                'command' => $request->get('command'),
                'auto_start' => true,
                'auto_restart' => true,
                'user' => 'codepier',
                'number_of_workers' => $request->get('number_of_workers'),
            ])
        );
    }

    /**
     * Display the specified resource.
     *
     * @param $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($siteId, $id)
    {
        return response()->json(
            SiteWorker::where('site_id', $siteId)->findOrFail($id)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId, $id)
    {
        return response()->json(
            SiteWorker::where('site_id', $siteId)->findOrFail($id)->delete()
        );
    }
}
