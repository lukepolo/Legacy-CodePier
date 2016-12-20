<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\SiteWorker;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteWorkerRequest;

class SiteWorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            SiteWorker::where('site_id', $siteId)->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SiteWorkerRequest $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SiteWorkerRequest $request, $siteId)
    {
        return response()->json(
            SiteWorker::create([
                'site_id' => $siteId,
                'command' => $request->get('command'),
                'auto_start' => true,
                'auto_restart' => true,
                'user' => $request->get('user'),
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
